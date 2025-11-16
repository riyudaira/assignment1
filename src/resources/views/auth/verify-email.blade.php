@extends('layouts.auth-app')

@section('content')
    <div class="verify-container">
        <h2>メール認証を完了してください</h2>
        <p>登録したメールアドレスに認証リンクを送信しました。</p>

        @if (session('message'))
            <div class="alert-success">{{ session('message') }}</div>
        @endif

        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit">認証メールを再送する</button>
        </form>
    </div>
@endsection
