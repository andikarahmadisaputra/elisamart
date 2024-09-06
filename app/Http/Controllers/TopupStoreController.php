<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\TopupStore;
use App\Models\Store;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TopupStoreController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:topup_store.list|topup_store.create|topup_store.cancel', ['only' => ['index']]);
        $this->middleware('permission:topup_store.create', ['only' => ['create','store']]);
        $this->middleware('permission:topup_store.edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:topup_store.cancel', ['only' => ['cancel']]);
        $this->middleware('permission:topup_store.approval', ['only' => ['underReview', 'approve', 'reject']]);
    }

    public function index(Request $request): View
    {
        $topupStores = TopupStore::select(['id', 'transaction_number', 'store_id', 'amount', 'note', 'status', 'created_at', 'updated_at', 'created_by'])
                                    ->orderBy('created_at', 'desc')
                                    ->paginate(10);
        
        return view('topup_store.index', compact('topupStores'))
                ->with('i', (request()->input('page', 1) - 1) * 10);
    }

    public function create(): View
    {
        $stores = Store::select(['id', 'name'])->get();
        
        return view('topup_store.create', compact('stores'));
    }

    public function store(Request $request): RedirectResponse
    {
        request()->validate([
            'store_id' => 'required|exists:stores,id',
            'amount' => 'required|numeric|min:0',
            'note' => 'nullable',
        ]);

        DB::beginTransaction();

        try {
            TopupStore::create([
                'store_id' => $request->input('store_id'),
                'amount' => $request->input('amount'),
                'note' => $request->input('note'),
                'status' => 'pending',
            ]);

            DB::commit();

            return redirect()->route('topup_store.index')
                        ->with('success', __('transaction.topup_store.message.create.success'));
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('topup_store.create failed: ' . $e->getMessage());

            return redirect()->route('topup_store.index')
                        ->with('error', __('transaction.topup_store.message.create.failed'));
        }
    }

    public function edit(TopupStore $topupStore): View
    {
        if ($topupStore->status == 'pending') {
            $stores = Store::select(['id', 'name'])->orderBy('name', 'asc')->get();

            return view('topup_store.edit', compact('topupStore', 'stores'));
        }
    }

    public function update(Request $request, TopupStore $topupStore): RedirectResponse
    {
        request()->validate([
            'store_id' => 'required|exists:stores,id',
            'amount' => 'required|numeric|min:0',
            'note' => 'nullable',
        ]);

        try {
            if ($topupStore->status === 'pending') {
                $topupStore->update($request->all());
    
                return redirect()->route('topup_store.index')
                                ->with('success', __('transaction.topup_store.message.update.success'));
            } 
        } catch (\Exception $e) {
            Log::error('topup_store.update failed: ' . $e->getMessage());

            return redirect()->route('topup_store.index')
                        ->with('error', __('transaction.topup_store.message.update.failed'));
        }
    }

    public function cancel(TopupStore $topupStore): RedirectResponse
    {
        try {
            if ($topupStore->status === 'pending') {
                $topupStore->status = 'canceled';
                $topupStore->save();
    
                return redirect()->back()->with('success', 'transaction.topup_store.message.cancel.success');
            }
        } catch (\Exception $e) {
            Log::error('topup_store.cancel failed: ' . $e->getMessage());

            return redirect()->route('topup_store.index')
                        ->with('error', __('transaction.topup_store.message.cancel.failed'));
        }
        
    }

    public function underReview(TopupStore $topupStore): RedirectResponse
    {
        try {
            if ($topupStore->status === 'pending') {
                $topupStore->status = 'under review';
                $topupStore->save();
    
                return redirect()->back()->with('success', __('transaction.topup_store.message.under_review.success'));
            } 
        } catch (\Exception $e) {
            Log::error('topup_store.underReview Failed: ' . $e->getMessage());

            return redirect()->route('topup_store.index')
                        ->with('error', __('transaction.topup_store.message.under_review.failed'));
        }
    }

    public function approve(TopupStore $topupStore): RedirectResponse
    {
        DB::beginTransaction();

        try {
            if ($topupStore->status === 'under review') {
                $topupStore->status = 'approved';
                $topupStore->save();
    
                $store = Store::findOrFail($topupStore->store_id);
                $store->balance += $topupStore->amount;
                $store->save();

                DB::commit();

                return redirect()->back()->with('success', __('transaction.topup_store.message.approve.success'));
            } 
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('topup_store.approve failed: ' . $e->getMessage());

            return redirect()->route('topup_store.index')
                        ->with('error', __('transaction.topup_store.message.approve.failed'));
        }
    }

    public function reject(TopupStore $topupStore): RedirectResponse
    {
        try {
            if ($topupStore->status === 'under review') {
                $topupStore->status = 'rejected';
                $topupStore->save();
    
                return redirect()->back()->with('success', __('transaction.topup_store.message.reject.success'));
            }
        } catch (\Exception $e) {
            Log::error('topup_store.reject failed: ' . $e->getMessage());

            return redirect()->route('topup_store.index')
                        ->with('error', __('transaction.topup_store.message.reject.failed'));
        }
    }
}
