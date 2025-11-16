@extends('layouts.auth-app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endsection
@section('content')
    <div class="auth-container">
        <h1 class="auth-title">会員登録</h1>

        <form method="POST" action="{{ route('register') }}" class="auth-form">
            @csrf

            <label for="name">ユーザー名</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}">
            @error('name')
                <div class="error-message">{{ $message }}</div>
            @enderror

            <label for="email">メールアドレス</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}">
            @error('email')
                <div class="error-message">{{ $message }}</div>
            @enderror

            <label for="password">パスワード</label>
            <input type="password" name="password" id="password">
            @error('password')
                <div class="error-message">{{ $message }}</div>
            @enderror

            <label for="password_confirmation">確認用パスワード</label>
            <input type="password" name="password_confirmation" id="password_confirmation">

            <button type="submit" class="auth-button">登録する</button>
        </form>

        <div class="auth-link">
            <a href="{{ route('login') }}">ログインはこちら</a>
        </div>
    </div>
@endsection
