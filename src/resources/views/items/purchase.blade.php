@extends('layouts.layout')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
@endsection

@section('content')
    <form action="{{ route('purchase.store', $item->id) }}" method="POST">
        @csrf
        <div class="purchase-container">
            <div class="purchase-left">
                {{-- 商品情報 --}}
                <div class="purchase-item">
                    <img src="{{ asset($item->image_path) }}" class="purchase-item-image" alt="商品画像">
                    <div class="purchase-item-info">
                        <h2 class="purchase-item-name">{{ $item->name }}</h2>
                        <p class="purchase-item-price">¥ {{ number_format($item->price) }}</p>
                    </div>
                </div>

                {{-- 支払い方法 --}}
                <div class="payment-section">
                    <h3>支払い方法</h3>
                    <select name="payment_method" class="payment-select">
                        <option value="" selected disabled>選択してください</option>
                        <option value="konbini">コンビニ払い</option>
                        <option value="card">カード払い</option>
                    </select>
                    @error('payment_method')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                {{-- 配送先 --}}
                @php
                    $address = session('purchase_address_' . $item->id);
                    $deliveryAddress = $address['address'] ?? Auth::user()->address;
                    $postCode = $address['post_code'] ?? Auth::user()->post_code;
                    $build = $address['build'] ?? Auth::user()->build;
                @endphp

                <div class="address-section">
                    <div class="address-container">
                        <h3>配送先</h3>
                        <a href="{{ route('purchase.address.edit', ['item' => $item->id]) }}" class="change-link">変更する</a>
                    </div>

                    <div class="address-info">
                        <p>〒 {{ $postCode }}</p>
                        <p>{{ $deliveryAddress }}</p>
                        <p>{{ $build }}</p>
                    </div>

                </div>

                <input type="hidden" name="delivery_address" value="{{ $deliveryAddress }}">
                @error('delivery_address')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
            {{-- 購入確認ボックス --}}
            <div class="purchase-right">
                <table class="purchase-summary-table">
                    <tr>
                        <th>商品代金</th>
                        <td>¥ {{ number_format($item->price) }}</td>
                    </tr>
                    <tr>
                        <th>支払い方法</th>
                        <td class="summary-payment-method">選択してください</td>
                    </tr>
                </table>


                <input type="hidden" name="payment_method" class="hidden-payment-method">
                <button type="submit" class="purchase-button">購入する</button>
            </div>
        </div>
    </form>
@endsection

@push('js')
    <script>
        document.querySelector('.payment-select').addEventListener('change', function() {
            const method = this.value;
            const methodLabel = this.options[this.selectedIndex].text;
            document.querySelector('.summary-payment-method').textContent = methodLabel;
            document.querySelector('.hidden-payment-method').value = methodLabel;
        });
    </script>
@endpush
