<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Purchase;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Http\Requests\PurchaseRequest;
use App\Http\Requests\AddressRequest;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;


class PurchaseController extends Controller
{
    //購入画面表示
    public function show(Item $item)
    {
        return view('items.purchase', compact('item'));
    }
    //購入の処理
    public function store(PurchaseRequest $request, Item $item)
    {
        $paymentMethod = $request->input('payment_method');
        $address = session('purchase_address_' . $item->id);
        // Stripe初期化
        Stripe::setApiKey(config('services.stripe.secret'));
        // 支払い方法をStripe用に変換
        $method = match ($paymentMethod) {
            'カード払い' => 'card',
            'コンビニ払い' => 'konbini',
            default => abort(400, '不正な支払い方法です'),
        };
        // Checkoutセッション作成
        $session = StripeSession::create([
            'payment_method_types' => [$method],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'jpy',
                    'unit_amount' => $item->price,
                    'product_data' => [
                        'name' => $item->name,
                    ],
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('purchase.success', ['item' => $item->id]),
            'cancel_url' => route('purchase.show', ['item' => $item->id]),
            'metadata' => [
                'user_id' => Auth::id(),
                'item_id' => $item->id,
                'payment_method' => $paymentMethod,
                'post_code' => $address['post_code'] ?? Auth::user()->post_code,
                'address' => $address['address'] ?? Auth::user()->address,
                'build' => $address['build'] ?? Auth::user()->build,
            ],
        ]);
        // Stripeの決済画面へリダイレクト
        return redirect($session->url);
    }
    public function success(Item $item)
    {
        $paymentMethod = session('last_payment_method', 'card');

        Purchase::create([
            'user_id'      => Auth::id(),
            'item_id'      => $item->id,
            'payment_method' => $paymentMethod,
            'post_code'    => Auth::user()->post_code,
            'address'      => Auth::user()->address,
            'build'        => Auth::user()->build,
            'purchased_at' => now(),
        ]);
        return redirect()->route('user.profile')->with('message', '購入が完了しました');
    }
    public function cancel(Item $item)
    {
        return redirect()->route('purchase.show', $item->id)
            ->with('error', '決済がキャンセルされました');
    }

    //送付先変更画面へ
    public function edit(Item $item)
    {
        $user = auth()->user();
        return view('items.address', compact('item', 'user'));
    }

    //送付先更新
    public function updateAddress(AddressRequest $request, Item $item)
    {
        session([
            'purchase_address_' . $item->id => [
                'post_code' => $request->input('post_code'),
                'address'   => $request->input('address'),
                'build'     => $request->input('build'),
            ]
        ]);

        return redirect()->route('purchase.show', ['item' => $item->id])
            ->with('message', '配送先を変更しました');
    }
}

        // バリデーション済みの値を取得
        // $payment = $request->input('payment_method');
        // $delivery = $request->input('delivery_address');

        // $address = session('purchase_address_' . $item->id);

        // Purchase::create([
        //     'user_id'      => Auth::id(),
        //     'item_id'      => $item->id,
        //     'payment_method' => $request->input('payment_method'),
        //     'post_code'    => $address['post_code'] ?? Auth::user()->post_code,
        //     'address'      => $address['address'] ?? Auth::user()->address,
        //     'build'        => $address['build'] ?? Auth::user()->build,
        //     'purchased_at'   => Carbon::now(),
        // ]);

        // return redirect()->route('items.index')->with('message', '購入が完了しました');
