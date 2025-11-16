<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\Auth\LoginController;
// use Illuminate\Support\Facades\Auth;
// use App\Http\Controllers\Auth\AuthController;

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
    ->middleware('auth')
    ->name('user.profile.edit');

// プロフィール更新処理
Route::post('/mypage/profile', [ProfileController::class, 'update'])
    ->middleware('auth')
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
