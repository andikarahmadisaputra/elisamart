<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PaymentRequest;
use App\Models\Store;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use PhpParser\Node\Stmt\TryCatch;

class PaymentRequestController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:payment_request.list|payment_request.create|payment_request.edit|payment_request.delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:payment_request.create', ['only' => ['create','store']]);
        $this->middleware('permission:payment_request.edit', ['only' => ['edit','update']]);
        $this->middleware('permission:payment_request.cancel', ['only' => ['cancel']]);
        $this->middleware('permission:payment_request.payment', ['only' => ['reviewPayment','confirmPayment']]);
    }

    public function index(Request $request): View
    {
        $paymentRequests = PaymentRequest::select('id', 'transaction_number', 'bon_number', 'store_id', 'user_id', 'amount', 'status', 'note', 'created_at', 'created_by')->orderBy('created_at', 'desc')->paginate('10');

        return view('payment_request.index', compact('paymentRequests'))
                ->with('i', (request()->input('page', 1) - 1) * 10);
    }

    public function create(): View
    {
        $stores = Store::select(['id', 'name'])->orderBy('name', 'asc')->get();

        return view('payment_request.create', compact('stores'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validatedData = $request->validate([
            'store_id' => 'required|exists:stores,id',
            'bon_number' => 'required',
            'amount' => 'required|numeric|min:0',
            'note' => 'nullable',
        ],
        [
            'store_id.required' => __('Store is required!'),
            'store_id.exists' => __('The selected store was not found.'),
            'bon_number.required' => __('Bon Number is required!'),
            'amount.required' => __('Amount is required!'),
            'amount.numeric' => __('Amount must be a number.'),
            'amount.min' => __('Amount must be greater than or equal to 0.'),
        ]);

        // Validasi khusus untuk kolom user (dinamis: email atau username)
        if (filter_var($request->user, FILTER_VALIDATE_EMAIL)) {
            $validatedData['user'] = $request->validate([
                'user' => 'required|exists:users,email',
            ],[
                'user.required' => 'User is required!',
                'user.exists' => 'The selected user was not found.',
            ])['user'];
        } else {
            $validatedData['user'] = $request->validate([
                'user' => 'required|exists:users,username',
            ],[
                'user.required' => 'User is required!',
                'user.exists' => 'The selected user was not found.',
            ])['user'];
        }
        
        $user = filter_var($request->user, FILTER_VALIDATE_EMAIL)
        ? User::where('email', $validatedData['user'])->first()
        : User::where('username', $validatedData['user'])->first();
        
        if ($user) {
            $user_id = $user->id;
        } else {
            return redirect()->back()->with('error', __('User Not Found'));
        }

        DB::beginTransaction();
        
        try {
            PaymentRequest::create([
                'store_id' => $validatedData['store_id'],
                'user_id' => $user_id,
                'bon_number' => $validatedData['bon_number'],
                'amount' => $validatedData['amount'],
                'note' => $validatedData['note'],
                'status' => 'awaiting payment',
            ]);

            DB::commit();

            return redirect()->route('payment_request.index')
                        ->with('success', __('transaction.payment_request.message.create.success'));
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Payment Request creation failed.', [
                'error_message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'stack_trace' => $e->getTraceAsString(),
                'request_data' => $request->all(),
                'user_active' => Auth::id(),
                'store_id' => $request->input('store_id'),
                'user_id' => $request->input('user_id'),
            ]);

            return redirect()->back()->with('error', __('transaction.payment_request.message.create.error'));
        }  
    }

    public function edit(PaymentRequest $paymentRequest): View
    {
        return view('payment_request.create', compact('paymentRequest'));
    }

    public function reviewPayment($id)
    {
        $paymentRequest = PaymentRequest::with('store')->with('user')->find($id);

        if (!$paymentRequest) {
            return response()->json(['error' => __('Payment Request Not Found')], 404);
        }
        
        return response()->json([
            'payment_request_id' => $paymentRequest->id,
            'transaction_number' => $paymentRequest->transaction_number,
            'bon_number' => $paymentRequest->bon_number,
            'amount' => number_format($paymentRequest->amount, 0, ',', '.'),
            'note' => $paymentRequest->note,
            'status' => $paymentRequest->status,
            'store' => $paymentRequest->store?->name,
            'user' => $paymentRequest->user?->name,
            'balance' => number_format($paymentRequest->user?->balance, 0, ',', '.'),
        ]);
    }

    public function confirmPayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'payment_request_id' => 'required|exists:payment_requests,id',
            'voucher' => 'required|numeric|min:0',
            'pin' => 'required|size:6',
        ],[
            'payment_request_id.required' => 'ID pembayaran harus ada!',
            'payment_request_id.exists' => 'ID pembayaran tidak ditemukan.',
            'voucher.required' => 'Voucher wajib diisi.',
            'voucher.numeric' => 'Voucher harus berupa angka.',
            'voucher.min' => 'Voucher harus lebih besar atau sama dengan 0',
            'pin.required' => 'PIN wajib diisi',
            'pin.size' => 'Panjang PIN harus 6 angka',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        $retries = 3;
        $attempts = 0;

        while ($attempts < $retries) {
            try {
                DB::beginTransaction();

                $paymentRequest = PaymentRequest::with(['store', 'user'])
                    ->where('id', $request->payment_request_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $paymentRequest->lockUserAndStore();

                if (!in_array($paymentRequest->status, ['awaiting payment'])) {
                    DB::rollBack();
                
                    $errorMessage = match ($paymentRequest->status) {
                        'canceled' => 'Tagihan telah dibatalkan oleh Admin',
                        'paid' => 'Tagihan sudah lunas',
                        default => 'Tagihan tidak menunggu pembayaran',
                    };
                
                    return response()->json([
                        'errors' => ['payment_request_id' => [$errorMessage]],
                    ], 400);
                }

                $user = $paymentRequest->user;
                $store = $paymentRequest->store;
                
                if ($request->voucher > $user->balance) {
                    DB::rollBack();
                    return response()->json([
                        'errors' => ['voucher' => ['Voucher yang digunakan harus lebih kecil atau sama dengan saldo yang tersisa']],
                    ], 400);
                }
    
                if ($request->voucher > $paymentRequest->amount) {
                    DB::rollback();
                    return response()->json([
                        'errors' => ['voucher' => ['Voucher yang digunakan tidak boleh lebih besar dari jumlah tagihan']],
                    ], 400);
                }
    
                if (!Hash::check($request->pin, $user->pin)) {
                    DB::rollBack();
                    return response()->json([
                        'errors' => ['pin' => ['PIN yang Anda masukkan salah']],
                    ], 400);
                }
        
                $user->balance -= $request->voucher;
                $user->save();
    
                $store->balance_in += $request->voucher;
                $store->save();
    
                $paymentRequest->status = 'paid';
                $paymentRequest->save();

                DB::commit();
                return response()->json([
                    'message' => 'Tagihan sudah berhasil dibayar',
                    'redirect' => route('payment_request.index')
                ], 200);

            } catch (QueryException $e) {
                if ($this->isDeadlockError($e)) {
                    DB::rollBack();
                    $attempts++;
                    sleep(1);  // Tunggu sejenak sebelum mencoba lagi
                    continue;  // Coba ulang transaksi
                } else {
                    DB::rollBack();
                    throw $e;  // Error lain, lempar ke atas
                }
            }

        }

        if ($attempts == $retries) {
            Log::error('Pembayaran gagal setelah beberapa percobaan: ' . $e->getMessage());
            return response()->json([
                'error' => ['system' => ['Pembayaran gagal dilakukan, silahkan hubungi Admin']],
            ], 400);
        }
    }

    private function isDeadlockError(QueryException $e)
    {
        return str_contains($e->getMessage(), 'Deadlock found when trying to get lock');
    }
}
