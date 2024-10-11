<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use App\Models\PaymentRequest;
use App\Models\Store;
use App\Models\User;
use App\Models\Transfer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MemberController extends Controller
{
    public function index(): View
    {
        return view('member.index');
    }

    public function payment(): View
    {
        $payments = PaymentRequest::select('id', 'transaction_number', 'amount', 'note', 'status', 'store_id', 'user_id', 'created_at')->where('user_id', Auth::user()->id)->where('status', 'awaiting payment')->get();

        return view('member.payment', compact('payments'));
    }

    public function profile(): View
    {
        return view('member.profile');
    }

    public function pin(): View
    {
        return view('member.pin');
    }

    public function updatePin(Request $request): RedirectResponse
    {
        $request->validate([
            'old_pin' => 'nullable|string|size:6|regex:/^[0-9]+$/',
            'pin' => 'required|string|size:6|regex:/^[0-9]+$/|same:confirm_pin',
            'confirm_pin' => 'required|string|size:6|regex:/^[0-9]+$/',
        ]);

        DB::beginTransaction();

        try {
            $user = User::findOrFail(Auth::user()->id);

            if (empty($user->pin)) {
                // If there's no existing PIN, hash and save the new PIN
                $user->pin = bcrypt($request->input('pin'));
                $user->save();
                DB::commit();

                return redirect()->route('member.profile')->with('success', 'PIN created successfully');
            } else {
                // Verify old PIN
                if (Hash::check($request->input('old_pin'), $user->pin)) {
                    $user->pin = bcrypt($request->input('pin'));
                    $user->save();
                    DB::commit();

                    return redirect()->route('member.profile')->with('success', 'PIN updated successfully');
                } else {
                    DB::rollBack();
                    return redirect()->back()->with('error', 'Old PIN does not match');
                }
            }
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Update PIN creation failed.', [
                'error_message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'stack_trace' => $e->getTraceAsString(),
                'request_data' => $request->all(),
                'user_id' => Auth::id(),
            ]);

            return redirect()->back()->with('error', 'Error updating PIN. Please try again.');
        }
    }

    public function updatePin2(Request $request): RedirectResponse
    {
        if (!empty(Auth::user()->pin)) {
            $this->validate($request, [
                'old_pin' => 'required|string|size:6|regex:/^[0-9]+$/',
                'pin' => 'required|string|size:6|regex:/^[0-9]+$/|same:confirm_pin',
            ]);
        } else {
            $this->validate($request, [
                'pin' => 'required|string|size:6|regex:/^[0-9]+$/|same:confirm_pin',
            ]);
        }

        DB::beginTransaction();

        try {
            if(empty(Auth::user()->pin)) {
                $user = User::findOrFail(Auth::user()->id);
                $user->pin = bcrypt($request->input('pin'));
                $user->save();
    
                DB::commit();

                return redirect()->route('member.profile')
                            ->with('success','PIN created successfully');
            } else {
                if (Hash::check($request->input('old_pin'), Auth::user()->pin)) {
                    $user = User::findOrFail(Auth::user()->id);
                    $user->pin = bcrypt($request->input('pin'));
                    $user->save();

                    DB::commit();

                    return redirect()->route('member.profile')
                            ->with('success','PIN updated successfully');

                } else {
                    DB::rollBack();

                    return redirect()->back()->with('error', 'PIN lama tidak sama');
                }
            }
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Update PIN creation failed.', [
                'error_message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'stack_trace' => $e->getTraceAsString(),
                'request_data' => $request->all(),
                'user_id' => Auth::id(),
                'pin' => $request->input('pin'),
                'confirm-pin' => $request->input('confirm-pin'),
                'old_pin' => $request->input('old_pin'),
            ]);

            return redirect()->back()->with('error', 'Error update PIN'.$e->getMessage());
        }
    }

    public function payWithVoucher($id)
    {
        $payment = PaymentRequest::select('id', 'transaction_number', 'amount', 'note', 'status', 'store_id', 'user_id', 'created_at')->where('id', $id)->first();

        if ($payment->status != 'awaiting payment') {
            return redirect()->route('member.payment_detail', $id);
        }

        return view('member.pay_with_voucher', compact('payment'));
    }

    public function confirmPayWithVoucher(Request $request, $id)
    {
        $payment = PaymentRequest::where('id', $id)->first();

        if ($payment->status != 'awaiting payment') {
            return redirect()->route('member.payment_detail', $id);
        }
        
        $request->validate([
            'voucher' => 'required|numeric|min:0',
            'pin' => 'required|string|size:6|regex:/^[0-9]+$/',
        ]);

        DB::beginTransaction();

        try {
            // First, check if the voucher is valid
            $voucherAmount = $request->input('voucher');
            $userBalance = Auth::user()->balance;

            if ($voucherAmount < 0) {
                Log::info('Voucher less than zero: ' . $request->input('voucher'));
                DB::rollBack();
                return redirect()->back()->with('error', 'Voucher yang digunakan tidak boleh kurang dari 0');
            }

            if ($voucherAmount > $userBalance) {
                Log::info('Voucher more than user balance: ' . $request->input('voucher'));
                DB::rollBack();
                return redirect()->back()->with('error', 'Voucher yang digunakan tidak boleh lebih dari ' . $userBalance);
            }

            if ($voucherAmount > $payment->amount) {
                Log::info('Voucher more than payment amount: ' . $payment->amount);
                DB::rollBack();
                return redirect()->back()->with('error', 'Voucher yang digunakan tidak boleh lebih dari ' . $payment->amount);
            }

            // Now, check the PIN
            if (Hash::check($request->input('pin'), Auth::user()->pin)) {
                // All checks passed, proceed with payment
                $paymentRequest = PaymentRequest::findOrFail($id);
                $paymentRequest->status = 'paid';
                $paymentRequest->voucher = $voucherAmount;
                $paymentRequest->save();

                $user = User::findOrFail(Auth::user()->id);
                $user->balance -= $voucherAmount;
                $user->save();

                $store = Store::findOrFail($paymentRequest->store_id);
                $store->balance_in += $voucherAmount;
                $store->save();

                DB::commit();

                return redirect()->route('member.payment_detail', $id)->with('success', 'Pembayaran Berhasil');
            } else {
                DB::rollBack();
                return redirect()->back()->with('error', 'PIN yang Anda masukkan salah');
            }
            
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Update PIN creation failed.', [
                'error_message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'stack_trace' => $e->getTraceAsString(),
                'request_data' => $request->all(),
                'user_id' => Auth::id(),
                'payment_request_id' => $id,
            ]);

            return redirect()->back()->with('error', 'Pembayaran gagal, silahkan hubungi Admin');
        }
    }

    public function paymentDetail($id): View
    {
        $payment = PaymentRequest::where('id', $id)->first();

        return view('member.payment_detail', compact('payment'));
    }

    public function history(): View
    {
        // Mendapatkan ID user yang sedang login
        $userId = Auth::id();

        // Query untuk tabel `topup_user_details` (Transaksi Top-Up)
        $topupQuery = DB::table('topup_user_details')
            ->join('topup_user_headers', 'topup_user_details.topup_user_header_id', '=', 'topup_user_headers.id')
            ->where('topup_user_details.user_id', $userId)
            ->where('topup_user_headers.status', 'approved')
            ->select(
                'topup_user_headers.updated_at as date',
                DB::raw("'topup' as transaction_name"),
                'topup_user_details.amount as amount'
            );

        // Query untuk tabel `transfers` (Transaksi Transfer)
        $transferQuery = DB::table('transfers')
            ->where(function($query) use ($userId) {
                $query->where('sender_id', $userId)->orWhere('recipient_id', $userId);
            })
            ->where('status', 'success')
            ->select(
                'updated_at as date',
                DB::raw("'transfer' as transaction_name"),
                DB::raw("CASE 
                            WHEN sender_id = $userId THEN -amount 
                            ELSE amount 
                        END as amount")
            );

        // Query untuk tabel `payments` (Transaksi Payment)
        $paymentQuery = DB::table('payment_requests')
            ->where('user_id', $userId)
            ->where('status', 'paid')
            ->select(
                'updated_at as date',
                DB::raw("'payment' as transaction_name"),
                DB::raw("-voucher as amount")
            );

        // Gabungkan ketiga query menggunakan `union`
        $transactions = $topupQuery
            ->unionAll($transferQuery)
            ->unionAll($paymentQuery)
            ->orderBy('date', 'desc')
            ->get();

        // Return hasilnya
        return view('member.history', compact('transactions'));
    }

    public function transfer(): View
    {
        return view('member.transfer');
    }

    public function storeTransfer(Request $request): RedirectResponse
    { 
        // Validasi input
        $request->validate([
            'recipient' => 'required|string',
            'amount' => 'required|numeric|min:1',
            'note' => 'nullable|string|max:255',
        ]);

        // Ambil input
        $recipientInput = $request->input('recipient');
        $amount = $request->input('amount');
        $note = $request->input('note');

        // Cari recipient berdasarkan email, NIK, atau nomor telepon
        $recipient = User::where('email', $recipientInput)
                        ->orWhere('nik', $recipientInput)
                        ->orWhere('phone', $recipientInput)
                        ->first();

        // Cek apakah recipient ditemukan
        if (!$recipient) {
            return redirect()->back()->with('error', 'Penerima tidak ditemukan.');
        }

        DB::beginTransaction();

        try {
            $transfer = Transfer::create([
                'sender_id' => Auth::id(),
                'recipient_id' => $recipient->id,
                'amount' => $amount,
                'note' => $note,
                'status' => 'pending',
            ]);

            DB::commit();

            return redirect()->route('member.transfer_preview', $transfer->id);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Transfer creation failed.', [
                'error_message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'stack_trace' => $e->getTraceAsString(),
                'request_data' => $request->all(),
                'user_id' => Auth::id(),
            ]);

            return redirect()->back()->with('error', 'Transaksi gagal, silahkan hubungi admin');
        }
    }

    public function transferPreview($id): View
    {
        $transfer = Transfer::where('id', $id)->first();

        return view('member.transfer_preview', compact('transfer'));
    }

    public function confirmTransfer(Request $request, $id)
    {
        $request->validate([
            'pin' => 'required|string|size:6|regex:/^[0-9]+$/',
        ]);

        DB::beginTransaction();

        try {
            if (Hash::check($request->input('pin'), Auth::user()->pin)) {
                // All checks passed, proceed with payment
                $transfer = Transfer::findOrFail($id);
                $transfer->status = 'success';
                $transfer->save();

                $sender = User::findOrFail($transfer->sender_id);
                $sender->balance -= $transfer->amount;
                $sender->save();

                $recipient = User::findOrFail($transfer->recipient_id);
                $recipient->balance += $transfer->amount;
                $recipient->save();

                DB::commit();

                return redirect()->route('member.transfer_detail', $id)->with('success', 'Transfer Berhasil');
            } else {
                DB::rollBack();
                
                return redirect()->back()->with('error', 'PIN yang Anda masukkan salah');
            }
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Confirm transfer failed.', [
                'error_message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'stack_trace' => $e->getTraceAsString(),
                'request_data' => $request->all(),
                'user_id' => Auth::id(),
            ]);

            return redirect()->back()->with('error', 'Transfer gagal, silahkan hubungi admin');
        }
    }

    public function transferDetail($id)
    {
        $transfer = Transfer::where('id', $id)->first();

        return view('member.transfer_detail', compact('transfer'));
    }
}
