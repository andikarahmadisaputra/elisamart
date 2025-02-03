<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\PaymentRequestController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\TopupStoreController;
use App\Http\Controllers\TopupUserController;
use App\Models\User;


Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('/login', [LoginController::class, 'login']);


Route::group(['middleware' => ['auth']], function() {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/', [MemberController::class, 'index'])->name('member.index');
    Route::get('profile', [MemberController::class, 'profile'])->name('member.profile');
    Route::get('pin', [MemberController::class, 'pin'])->name('member.pin');
    Route::put('pin/store', [MemberController::class, 'updatePin'])->name('member.update_pin');
    Route::get('transfer', [MemberController::class, 'transfer'])->name('member.transfer');
    Route::put('transfer/store_transfer', [MemberController::class, 'storeTransfer'])->name('member.store_transfer');
    Route::get('transfer/{id}/transfer_preview', [MemberController::class, 'transferPreview'])->name('member.transfer_preview');
    Route::put('transfer/{id}/confirm_transfer', [MemberController::class, 'confirmTransfer'])->name('member.confirm_transfer');
    Route::get('transfer/{id}/detail', [MemberController::class, 'transferDetail'])->name('member.transfer_detail');
    Route::get('payment', [MemberController::class, 'payment'])->name('member.payment');
    Route::get('payment/{id}/pay_with_voucher', [MemberController::class, 'payWithVoucher'])->name('member.pay_with_voucher');
    Route::put('payment/{id}/confirm_pay_with_voucher', [MemberController::class, 'confirmPayWithVoucher'])->name('member.confirm_pay_with_voucher');
    Route::get('payment/{id}/detail', [MemberController::class, 'paymentDetail'])->name('member.payment_detail');
    Route::get('history', [MemberController::class, 'history'])->name('member.history');


    Route::resource('admin/roles', RoleController::class);
    Route::resource('admin/users', UserController::class);
    Route::resource('admin/tags', TagController::class);
    Route::resource('admin/stores', StoreController::class);
    
    Route::get('users-export', [UserController::class, 'export'])->name('users.export');
    Route::post('users-import', [UserController::class, 'import'])->name('users.import');

    Route::get('/admin', [HomeController::class, 'index'])->name('admin');
    Route::get('admin/topup_store', [TopupStoreController::class, 'index'])->name('topup_store.index');
    Route::get('admin/topup_store/create', [TopupStoreController::class, 'create'])->name('topup_store.create');
    Route::post('admin/topup_store/store', [TopupStoreController::class, 'store'])->name('topup_store.store');
    Route::get('admin/topup_store/{topupStore}/edit', [TopupStoreController::class, 'edit'])->name('topup_store.edit');
    Route::put('admin/topup_store/{topupStore}/update', [TopupStoreController::class, 'update'])->name('topup_store.update');
    Route::patch('admin/topup_store/{topupStore}/cancel', [TopupStoreController::class, 'cancel'])->name('topup_store.cancel');
    Route::patch('admin/topup_store/{topupStore}/under_review', [TopupStoreController::class, 'underReview'])->name('topup_store.under_review');
    Route::patch('admin/topup_store/{topupStore}/approve', [TopupStoreController::class, 'approve'])->name('topup_store.approve');
    Route::patch('admin/topup_store/{topupStore}/reject', [TopupStoreController::class, 'reject'])->name('topup_store.reject');

    Route::get('admin/topup_user', [TopupUserController::class, 'index'])->name('topup_user.index');
    Route::get('admin/topup_user/{topupUserHeader}/show', [TopupUserController::class, 'show'])->name('topup_user.show');
    Route::get('admin/topup_user/create_by_tag', [TopupUserController::class, 'createByTag'])->name('topup_user.create_by_tag');
    Route::get('admin/topup_user/create_by_user', [TopupUserController::class, 'createByUser'])->name('topup_user.create_by_user');
    Route::post('admin/topup_user/store_by_tag', [TopupUserController::class, 'storeByTag'])->name('topup_user.store_by_tag');
    Route::get('admin/topup_store/{topupUserHeader}/edit', [TopupUserController::class, 'edit'])->name('topup_user.edit');
    Route::patch('admin/topup_user/{topupUserHeader}/cancel', [TopupUserController::class, 'cancel'])->name('topup_user.cancel');
    Route::patch('admin/topup_user/{topupUserHeader}/under_review', [TopupUserController::class, 'underReview'])->name('topup_user.under_review');
    Route::patch('admin/topup_user/{topupUserHeader}/approve', [TopupUserController::class, 'approve'])->name('topup_user.approve');
    Route::patch('admin/topup_user/{topupUserHeader}/reject', [TopupUserController::class, 'reject'])->name('topup_user.reject');

    Route::get('admin/payment_request', [PaymentRequestController::class, 'index'])->name('payment_request.index');
    Route::get('admin/payment_request/create', [PaymentRequestController::class, 'create'])->name('payment_request.create');
    Route::post('admin/payment_request/store', [PaymentRequestController::class, 'store'])->name('payment_request.store');
    Route::get('admin/payment_request/{paymentRequest}/edit', [PaymentRequestController::class, 'edit'])->name('payment_request.edit');
    Route::patch('admin/payment_request/{paymentRequest}/update', [PaymentRequestController::class, 'update'])->name('payment_request.update');
    Route::patch('admin/payment_request/{paymentRequest}/cancel', [PaymentRequestController::class, 'cancel'])->name('payment_request.cancel');
    Route::get('admin/payment_request/{id}/review', [PaymentRequestController::class, 'reviewPayment']);
    Route::post('admin/payment_request/confirm', [PaymentRequestController::class, 'confirmPayment']);
});

Route::get('/api/users', function (Illuminate\Http\Request $request) {
    $query = $request->get('query', '');
    $users = User::where('name', 'like', "%{$query}%")
        ->orWhere('username', 'like', "%{$query}%")
        ->orWhere('email', 'like', "%{$query}%")
        ->limit(10)
        ->get(['id', 'name', 'username', 'email', 'balance']);
    return response()->json($users);
})->middleware(['auth', 'can:user.list']);