<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Purchase;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Http\Requests\PurchaseRequest;
use App\Http\Requests\AddressRequest;


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
        // バリデーション済みの値を取得
        $payment = $request->input('payment_method');
        $delivery = $request->input('delivery_address');

        $address = session('purchase_address_' . $item->id);

        Purchase::create([
            'user_id'      => Auth::id(),
            'item_id'      => $item->id,
            'payment_method' => $request->input('payment_method'),
            'post_code'    => $address['post_code'] ?? Auth::user()->post_code,
            'address'      => $address['address'] ?? Auth::user()->address,
            'build'        => $address['build'] ?? Auth::user()->build,
            'purchased_at'   => Carbon::now(),
        ]);

        return redirect()->route('user.profile')->with('message', '購入が完了しました');
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
