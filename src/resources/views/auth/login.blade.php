@extends('layouts.auth-app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endsection

@section('content')
    <div class="auth-container">
        <h1 class="auth-title">ログイン</h1>
        <form method="POST" action="{{ route('login') }}" class="auth-form">
            @csrf
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
            <button type="submit" class="auth-button">ログインする</button>
        </form>
        <div class="auth-link">
            <a href="{{ route('register') }}">会員登録はこちら</a>
        </div>
    </div>
@endsection
