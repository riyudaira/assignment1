<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ChatController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

//メール認証
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect()->route('user.profile.edit');
})->middleware(['auth', 'signed'])->name('verification.verify');

//商品一覧画面
Route::get('/', [ItemController::class, 'index'])->name('items.index');

//商品詳細画面
Route::get('/item/{item}', [ItemController::class, 'detail'])->name('items.detail');

//商品詳細:コメント追加機能
Route::post('/item/{item}/comment', [CommentController::class, 'store'])->name('comments.store')->middleware('auth');;

//商品詳細:いいねアイコン押下でカウント機能
Route::post('/item/{item}/like', [ItemController::class, 'like'])
    ->middleware('auth')
    ->name('item.like');

//プロフィール画面(ミドルウェア)
Route::get('/mypage', [ProfileController::class, 'profile'])
    ->middleware('auth')
    ->name('user.profile');

// プロフィール編集画面（ミドルウェア）
Route::get('/mypage/profile', [ProfileController::class, 'edit'])
    ->middleware(['auth', 'verified'])
    ->name('user.profile.edit');

// プロフィール更新処理
Route::post('/mypage/profile', [ProfileController::class, 'update'])
    ->middleware(['auth'])
    ->name('user.profile.update');

//出品画面(ミドルウェア)
Route::get('/items/sell', [ItemController::class, 'sell'])
    ->middleware('auth')
    ->name('items.sell');

//出品情報登録
Route::post('/items', [ItemController::class, 'store'])
    ->middleware('auth')
    ->name('item.store');

//購入画面(ミドルウェア)
Route::get('/purchase/{item}', [PurchaseController::class, 'show'])
    ->middleware('auth')
    ->name('purchase.show');

//購入の確定処理
Route::post('/purchase/{item}', [PurchaseController::class, 'store'])
    ->middleware('auth')
    ->name('purchase.store');

//stripe購入確定
Route::get('/purchase/{item}/success', [PurchaseController::class, 'success'])
    ->middleware('auth')
    ->name('purchase.success');

//stripe購入失敗
Route::get('/purchase/{item}/cancel', [PurchaseController::class, 'cancel'])
    ->middleware('auth')
    ->name('purchase.cancel');

//送付先変更画面(ミドルウェア)
Route::get('/purchase/address/{item}', [PurchaseController::class, 'edit'])
    ->middleware('auth')
    ->name('purchase.address.edit');

//送付先変更の確定処理
Route::put('/purchase/address/{item}', [PurchaseController::class, 'updateAddress'])
    ->middleware('auth')
    ->name('purchase.address.update');

// チャット画面表示
Route::get('/items/chat/{item}', [ChatController::class, 'show'])
    ->middleware('auth')
    ->name('chat.show');

// メッセージ送信処理
Route::post('/items/chat/{item}', [ChatController::class, 'store'])
    ->middleware('auth')
    ->name('chat.store');

// メッセージ編集
Route::patch('/chat/message/{chat}', [ChatController::class, 'update'])->name('chat.update');

// メッセージ削除
Route::delete('/chat/message/{chat}', [ChatController::class, 'destroy'])->name('chat.destroy');

// 取引完了（購入者が「完了」ボタンを押した時の処理）
Route::post('/items/chat/{item}/complete', [ChatController::class, 'complete'])
    ->middleware('auth')
    ->name('purchase.complete');

// 評価送信（購入者・出品者共通）
Route::post('/items/{item}/review', [ChatController::class, 'storeReview'])
    ->middleware('auth')
    ->name('items.review.store');
