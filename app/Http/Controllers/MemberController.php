<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use App\Models\PaymentRequest;
use App\Models\Store;
use App\Models\User;
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

        return view('member.voucher', compact('payment'));
    }

    public function pay(Request $request, $id)
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


    public function pay2(Request $request, $id)
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
            if(Hash::check($request->input('pin'), Auth::user()->pin) && $request->input('voucher') >= 0 && $request->input('voucher') <= Auth::user()->balance) {
                $paymentRequest = PaymentRequest::findOrFail($id);
                $paymentRequest->status = 'paid';
                $paymentRequest->voucher = $request->input('voucher');
                $paymentRequest->save();

                $user = User::findOrFail(Auth::user()->id);
                $user->balance -= $request->input('voucher');
                $user->save();

                $store = Store::findOrFail(PaymentRequest::find($id)->store_id);
                $store->balance_in += $request->input('voucher');
                $store->save();

                DB::commit();

                return redirect()->route('member.payment_detail', $id)->with('success', 'Pembayaran Berhasil');
            } elseif (Hash::check($request->input('pin'), Auth::user()->pin)) {
                DB::rollBack();

                return redirect()->back()->with('error', 'PIN yang Anda masukkan salah');
            } elseif ($request->input('voucher') < 0) {
                DB::rollBack();

                return redirect()->back()->with('error', 'Voucher yang digunakan tidak boleh kurang dari 0');
            } elseif ($request->input('voucher') > Auth::user()->balance) {
                DB::rollBack();

                return redirect()->back()->with('error', 'Voucher yang digunakan tidak boleh lebih dari '. Auth::user()->balance);
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
                'payment_request_id' => PaymentRequest::find($id),
            ]);

            return redirect()->back()->with('error', 'Pembayaran gagal, silahkan hubungi Admin');
        }
        
    }

    public function detail($id)
    {
        $payment = PaymentRequest::where('id', $id)->first();

        return view('member.detail', compact('payment'));
    }
}
