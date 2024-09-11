<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\PaymentRequestController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\TopupStoreController;
use App\Http\Controllers\TopupUserController;
use Illuminate\Support\Facades\Auth;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', [MemberController::class, 'index'])->name('member.index');

Auth::routes();
Auth::routes(['register' => false]);

Route::get('/admin', [HomeController::class, 'index'])->name('admin');

Route::group(['middleware' => ['auth']], function() {
    Route::resource('roles', RoleController::class);
    Route::resource('users', UserController::class);
    Route::resource('tags', TagController::class);
    Route::resource('stores', StoreController::class);

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
});