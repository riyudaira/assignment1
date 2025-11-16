@extends('layouts.layout')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/address.css') }}">
@endsection

@section('content')
    <div class="address-edit-container">
        <h1 class="address-title">住所の変更</h1>

        <form method="POST" action="{{ route('purchase.address.update', ['item' => $item->id]) }}" class="address-form">
            @csrf
            @method('PUT')

            <label for="post_code">郵便番号</label>
            <input type="text" name="post_code" id="post_code" value="{{ old('post_code', $user->post_code) }}">
            @error('post_code')
                <div class="error-message">{{ $message }}</div>
            @enderror

            <label for="address">住所</label>
            <input type="text" name="address" id="address" value="{{ old('address', $user->address) }}">
            @error('address')
                <div class="error-message">{{ $message }}</div>
            @enderror

            <label for="build">建物名</label>
            <input type="text" name="build" id="build" value="{{ old('build', $user->build) }}">
            @error('build')
                <div class="error-message">{{ $message }}</div>
            @enderror

            <button type="submit" class="address-update-button">更新する</button>
        </form>
    </div>
@endsection
