<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\VerificationController;

//メール認証の遷移
Auth::routes(['verify' => true]);

//メール認証の設定
Route::get('/email/verified-redirect', [HomeController::class, 'redirectAfterVerify'])
    ->middleware('auth')
    ->name('verified.redirect');
Route::get('/email/verify/{id}/{hash}', VerificationController::class)
    ->middleware(['auth', 'signed'])
    ->name('verification.verify');
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

//検索結果表示・商品一覧へ遷移
Route::get('/', [ItemController::class, 'index'])->name('items.index');

//商品詳細へ遷移
Route::get('/item/{item}', [ItemController::class, 'detail'])->name('items.detail');

//商品詳細:コメント追加機能
Route::post('/item/{item}/comment', [CommentController::class, 'store'])->name('comments.store');

//いいねアイコン押下でカウント機能
Route::post('/item/{item}/like', [ItemController::class, 'like'])
    ->middleware('auth')
    ->name('item.like');

//マイページ画面へ遷移(ミドルウェア)
Route::get('/mypage', [ProfileController::class, 'profile'])
    ->middleware('auth')
    ->name('user.profile');

// プロフィール編集画面へ遷移（edit.blade.php を表示）
Route::get('/mypage/profile', [ProfileController::class, 'edit'])
    ->middleware(['auth'])
    ->name('user.profile.edit');

// プロフィール更新処理
Route::post('/mypage/profile', [ProfileController::class, 'update'])
    ->middleware(['auth'])
    ->name('user.profile.update');

//出品画面へ遷移(ミドルウェア)
Route::get('/items/sell', [ItemController::class, 'sell'])
    ->middleware('auth')
    ->name('items.sell');

//出品情報登録
Route::post('/items', [ItemController::class, 'store'])
    ->middleware('auth')
    ->name('item.store');

//購入画面遷移
Route::get('/purchase/{item}', [PurchaseController::class, 'show'])
    ->middleware('auth')
    ->name('purchase.show');

//購入の確定処理
Route::post('/purchase/{item}', [PurchaseController::class, 'store'])
    ->middleware('auth')
    ->name('purchase.store');

//送付先変更画面へ遷移
Route::get('/purchase/address/{item}', [PurchaseController::class, 'edit'])
    ->middleware('auth')
    ->name('purchase.address.edit');

//送付先変更の確定処理
Route::put('/purchase/address/{item}', [PurchaseController::class, 'updateAddress'])
    ->middleware('auth')
    ->name('purchase.address.update');

//ログインルート
Route::post('/login', [LoginController::class, 'login'])->name('login');

//stripe決済画面遷移(成功)
Route::get('/purchase/{item}/success', [PurchaseController::class, 'success'])
    ->middleware('auth')
    ->name('purchase.success');

//stripe決済画面遷移(キャンセル)
Route::get('/purchase/{item}/cancel', [PurchaseController::class, 'cancel'])
    ->middleware('auth')
    ->name('purchase.cancel');
