<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\Tag;
use App\Models\TopupUserDetail;
use App\Models\TopupUserHeader;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class TopupUserController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:topup_user.list|topup_user.create|topup_user.cancel', ['only' => ['index']]);
        $this->middleware('permission:topup_user.create', ['only' => ['create','store']]);
        $this->middleware('permission:topup_user.edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:topup_user.cancel', ['only' => ['cancel']]);
        $this->middleware('permission:topup_user.approval', ['only' => ['underReview', 'approve', 'reject']]);
    }

    public function index(Request $request): View
    {
        $topupUserHeaders = TopupUserHeader::select('id', 'transaction_number', 'store_id', 'total_user', 'total_amount', 'note', 'status', 'created_at', 'created_by')->orderBy('created_at', 'desc')->paginate(20);

        return view('topup_user_header.index', compact('topupUserHeaders'))
                ->with('i', (request()->input('page', 1) - 1) * 20);
    }

    public function show(TopupUserHeader $topupUserHeader)
    {
        $topupUserDetails = $topupUserHeader->topupUserDetails;

        return view('topup_user_header.show', compact('topupUserHeader', 'topupUserDetails'))
                ->with('i', (request()->input('page', 1) - 1) * 10);
    }

    public function createByTag(): View
    {
        $stores = Store::select('id', 'name')->orderBy('name', 'asc')->get();

        $tags = Tag::select('id', 'name')->orderBy('name', 'asc')->get();

        return view('topup_user_header.create_by_tag', compact('stores', 'tags'));
    }

    public function storeByTag(Request $request): RedirectResponse
    {
        $validatedData = $request->validate([
            'store_id' => 'required|exists:stores,id',
            'tag_id' => 'required|exists:tags,id',
            'amount' => 'required|numeric|min:0',
            'note' => 'nullable',
        ],
        [
            'store_id.required' => __('Store is required!'),
            'store_id.exists' => __('The selected store was not found.'),
            'tag_id.required' => __('Tag is required!'),
            'tag_id.exists' => __('The selected tag was not found.'),
            'amount.required' => __('Amount is required!'),
            'amount.numeric' => __('Amount must be a number.'),
            'amount.min' => __('Amount must be greater than or equal to 0.'),
        ]);
        
        DB::beginTransaction();
        
        try {
            // Ambil tag dengan fail-safe
            $tag = Tag::findOrFail($validatedData['tag_id']);
            $users = $tag->users;

            if ($users->isEmpty()) {
                return redirect()->route('topup_user.index')
                    ->with('error', __('No users found for the selected tag.'));
            }

            $totalUser = $users->count();
            $totalAmount = $totalUser * $validatedData['amount'];
            
            // Simpan header transaksi
            $topupUserHeader = TopupUserHeader::create([
                'store_id' => $validatedData['store_id'],
                'total_user' => $totalUser,
                'total_amount' => $totalAmount,
                'note' => $validatedData['note'] ?? '', // Gunakan default kosong jika null
                'status' => 'pending',
            ]);
            
            // Simpan detail transaksi
            foreach ($users as $user) {
                TopupUserDetail::create([
                    'topup_user_header_id' => $topupUserHeader->id,
                    'user_id' => $user->id,
                    'amount' => $validatedData['amount'],
                ]);
            }
            
            DB::commit();
            
            return redirect()->route('topup_user.index')
            ->with('success', __('transaction.topup_user.message.create.success'));
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Topup User creation failed.', [
                'error_message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'stack_trace' => $e->getTraceAsString(),
                'request_data' => $request->all(),
                'user_id' => Auth::id(),
                'store_id' => $request->input('store_id'),
                'tag_id' => $request->input('tag_id'),
            ]);
            
            return redirect()->route('topup_user.index')
            ->with('error', __('transaction.topup_user.message.create.failed'));
        }
    }
    
    public function createByUser(): View
    {
        $stores = Store::select('id', 'name')->orderBy('name', 'asc')->get();

        return view('topup_user_header.create_by_user', compact('stores'));
    }

    public function storeByUser(Request $request)
    {
        $validatedData = $request->validate([
            'store_id' => 'required|exists:stores,id',
            'user' => 'required|exists:tags,id',
            'amount' => 'required|numeric|min:0',
            'note' => 'nullable',
        ],[
            'store_id.required' => __('Store is required!'),
            'store_id.exists' => __('The selected store was not found.'),
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
            $topupUserHeader = TopupUserHeader::create([
                'store_id' => $validatedData['store_id'],
                'total_user' => 1,
                'total_amount' => $validatedData['amount'],
                'note' => $validatedData['note'],
                'status' => 'pending',
            ]);
            
            TopupUserDetail::create([
                'topup_user_header_id' => $topupUserHeader->id,
                'user_id' => $user_id,
                'amount' => $validatedData['amount'],
            ]);

            DB::commit();
            
            return redirect()->route('topup_user.index')
            ->with('success', __('transaction.topup_user.message.create.success'));
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Topup User creation failed.', [
                'error_message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'stack_trace' => $e->getTraceAsString(),
                'request_data' => $request->all(),
                'user_id' => Auth::id(),
                'store_id' => $request->input('store_id'),
                'user' => $request->input('user'),
            ]);
            
            return redirect()->route('topup_user.index')
            ->with('error', __('transaction.topup_user.message.create.failed'));
        }
    }

    public function underReview(TopupUserHeader $topupUserHeader): RedirectResponse
    {
        try {
            if ($topupUserHeader->status !== 'pending') {
                return redirect()->back()->with('error', __('Can not change the transaction status because the status is not pending'));
            }

            $topupUserHeader->status = 'under review';
            $topupUserHeader->save();

            return redirect()->back()->with('success', __('transaction.topup_user.message.under_review.success'));
        } catch (\Exception $e) {
            Log::error('topup_user.underReview Failed: ' . $e->getMessage());

            return redirect()->back()->with('error', __('transaction.topup_user.message.under_review.failed'));
        }
    }

    public function approve(TopupUserHeader $topupUserHeader): RedirectResponse
    {
        DB::beginTransaction(); // Mulai transaksi lebih awal

        try {
            // Cek status harus "under review"
            if ($topupUserHeader->status !== 'under review') {
                return redirect()->back()->with('error', 'Topup User Header is not under review.');
            }

            // Lock saldo toko agar tidak ada perubahan selama transaksi
            $store = Store::where('id', $topupUserHeader->store_id)->lockForUpdate()->firstOrFail();

            // Cek saldo toko cukup atau tidak
            if ($store->balance < $topupUserHeader->total_amount) {
                DB::rollBack(); // Rollback transaksi jika saldo tidak cukup
                return redirect()->back()->with('error', 'Insufficient store balance.');
            }

            // Kurangi saldo toko
            $store->balance -= $topupUserHeader->total_amount;
            $store->save();

            // Ambil semua detail topup
            $topupUserDetails = $topupUserHeader->topupUserDetails;

            // Update saldo user tanpa perlu `lockForUpdate()`
            foreach ($topupUserDetails as $detail) {
                User::where('id', $detail->user_id)->increment('balance', $detail->amount);
            }

            // Update status transaksi
            $topupUserHeader->status = 'approved';
            $topupUserHeader->save();

            DB::commit(); // Commit transaksi jika semua berhasil

            return redirect()->back()->with('success', 'Amounts updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack(); // Pastikan rollback hanya di sini

            Log::error('Topup User approval failed.', [
                'error_message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'stack_trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
                'topup_user_header_id' => $topupUserHeader->id,
                'store_id' => $topupUserHeader->store_id,
            ]);

            return redirect()->back()->with('error', 'An error occurred while processing the approval.');
        }
    }

    public function reject(TopupUserHeader $topupUserHeader): RedirectResponse
    {
        try {
            if ($topupUserHeader->status !== 'under review') {
                return redirect()->back()->with('error', __('transaction.topup_user.message.under_review.error.not_pending'));
            }

            $topupUserHeader->status = 'reject';
            $topupUserHeader->save();
    
            return redirect()->back()->with('success', __('transaction.topup_user.message.under_review.success'));
        } catch (\Exception $e) {
            Log::error('topup_user.reject Failed: ' . $e->getMessage());

            return redirect()->back()->with('error', __('transaction.topup_user.message.under_review.failed'));
        }
    }
}
