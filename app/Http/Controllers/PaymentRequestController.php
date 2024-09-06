<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PaymentRequest;
use App\Models\Store;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class PaymentRequestController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:payment_request.list|payment_request.create|payment_request.edit|payment_request.delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:payment_request.create', ['only' => ['create','store']]);
        $this->middleware('permission:payment_request.edit', ['only' => ['edit','update']]);
        $this->middleware('permission:payment_request.delete', ['only' => ['destroy']]);
    }

    public function index(Request $request): View
    {
        $paymentRequests = PaymentRequest::select('id', 'store_id', 'user_id', 'amount', 'status', 'created_at')->orderBy('created_at', 'desc')->paginate('10');

        return view('payment_request.index', compact('paymentRequests'))
                ->with('i', (request()->input('page', 1) - 1) * 10);
    }

    public function create(): View
    {
        $stores = Store::select(['id', 'name'])->orderBy('name', 'asc')->get();

        $users = User::select(['id', 'nik', 'name'])->orderBy('name', 'asc')->get();

        return view('payment_request.create', compact('stores', 'users'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'store_id' => 'required|exists:stores,id',
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0',
            'note' => 'nullable',
        ]);

        DB::beginTransaction();

        try {
            PaymentRequest::create([
                'store_id' => $request->input('store_id'),
                'user_id' => $request->input('user_id'),
                'amount' => $request->input('amount'),
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
}
