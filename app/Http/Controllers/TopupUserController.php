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
        $topupUserHeaders = TopupUserHeader::select('id', 'transaction_number', 'store_id', 'total_user', 'total_amount', 'note', 'status', 'created_at', 'created_by')->orderBy('created_at', 'desc')->paginate(10);

        return view('topup_user_header.index', compact('topupUserHeaders'))
                ->with('i', (request()->input('page', 1) - 1) * 10);
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

    public function createByUser(): View
    {
        $stores = Store::select('id', 'name')->orderBy('name', 'asc')->get();

        $users = User::select('id', 'nik', 'name')->orderBy('name', 'asc')->get();

        return view('topup_user_header.create_by_user', compact('stores', 'users'));
    }

    public function storeByTag(Request $request): RedirectResponse
    {
        $request->validate([
            'store_id' => 'required|exists:stores,id',
            'tag_id' => 'required|exists:tags,id',
            'amount' => 'required|numeric|min:0',
            'note' => 'nullable',
        ]);

        DB::beginTransaction();

        try {
            $tag = Tag::find($request->input('tag_id'));
            $users = $tag->users;
            $totalUser = $users->count();

            $totalAmount = $totalUser * $request->input('amount');

            $topupUserHeader = TopupUserHeader::create([
                'store_id' => $request->input('store_id'),
                'total_user' => $totalUser,
                'total_amount' => $totalAmount,
                'note' => $request->input('note'),
                'status' => 'pending',
            ]);

            foreach ($users as $user) {
                TopupUserDetail::create([
                    'topup_user_header_id' => $topupUserHeader->id,
                    'user_id' => $user->id,
                    'amount' => $request->input('amount'),
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

    public function storeByUser()
    {

    }

    public function underReview(TopupUserHeader $topupUserHeader): RedirectResponse
    {
        try {
            if ($topupUserHeader->status === 'pending') {
                $topupUserHeader->status = 'under review';
                $topupUserHeader->save();
    
                return redirect()->back()->with('success', __('transaction.topup_user.message.under_review.success'));
            } else {
                return redirect()->back()->with('error', __('transaction.topup_user.message.under_review.error.not_pending'));
            }
        } catch (\Exception $e) {
            Log::error('topup_user.underReview Failed: ' . $e->getMessage());

            return redirect()->back()->with('error', __('transaction.topup_user.message.under_review.failed'));
        }
    }

    public function approve(TopupUserHeader $topupUserHeader): RedirectResponse
    {
        DB::beginTransaction();

        try {
            if ($topupUserHeader->status === 'under review') {
                if ($topupUserHeader->store->balance >= $topupUserHeader->total_amount) {
                    $store = Store::findOrFail($topupUserHeader->store_id);
                    $store->balance -= $topupUserHeader->total_amount;
                    $store->save();

                    $topupUserDetails = $topupUserHeader->topupUserDetails;

                    foreach ($topupUserDetails as $detail) {
                        User::where('id', $detail->user_id)->increment('balance', $detail->amount);
                    }

                    $topupUserHeader->status = 'approved';
                    $topupUserHeader->save();

                    DB::commit();

                    return redirect()->back()->with('success', 'Amounts updated successfully.');
                } else {
                    DB::rollBack();
                    return redirect()->back()->with('error', 'Insufficient store balance.');
                }
            } else {
                DB::rollBack();
                return redirect()->back()->with('error', 'Topup User Header is not under review.');
            }
        } catch (\Exception $e) {
            DB::rollBack();

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
            if ($topupUserHeader->status === 'under review') {
                $topupUserHeader->status = 'reject';
                $topupUserHeader->save();
    
                return redirect()->back()->with('success', __('transaction.topup_user.message.under_review.success'));
            } else {
                return redirect()->back()->with('error', __('transaction.topup_user.message.under_review.error.not_pending'));
            }
        } catch (\Exception $e) {
            Log::error('topup_user.reject Failed: ' . $e->getMessage());

            return redirect()->back()->with('error', __('transaction.topup_user.message.under_review.failed'));
        }
    }
}
