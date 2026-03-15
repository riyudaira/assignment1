@extends('layouts.auth-app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/verify-email.css') }}">
@endsection

@section('content')
    <div class="verify-wrapper">
        <p class="verify-text">
            登録していただいたメールアドレスに認証メールを送付しました。<br>
            メール認証を完了してください。
        </p>
        @if (session('message'))
            <div class="alert-success">{{ session('message') }}</div>
        @endif
        <div class="verify-buttons">
            <a href="mailto:{{ Auth::check() ? Auth::user()->email : '' }}" class="btn-main">
                認証はこちらから
            </a>
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="btn-link">
                    認証メールを再送する
                </button>
            </form>
        </div>
    </div>
@endsection
