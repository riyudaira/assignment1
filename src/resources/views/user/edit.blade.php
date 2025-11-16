@extends('layouts.layout')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/edit.css') }}">
@endsection

@section('content')
    <div class="profile-edit-container">
        <h1 class="profile-edit-title">プロフィール設定</h1>

        <form method="POST" action="{{ route('user.profile.update') }}" enctype="multipart/form-data"
            class="profile-edit-form">
            @csrf

            {{-- プロフィール画像 --}}
            <div class="profile-image-wrapper">
                <img src="{{ Auth::user()->profile_image ?? asset('images/user-placeholder.png') }}"
                    class="profile-image-preview" alt="プロフィール画像">
                <label class="image-select-btn">
                    画像を選択する
                    <input type="file" name="profile_image" accept="image/*" class="image-input">
                </label>
                @error('profile_image')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            {{-- 入力フォーム --}}
            <label for="name">ユーザー名</label>
            <input type="text" name="name" id="name" value="{{ old('name', Auth::user()->name) }}">
            @error('name')
                <div class="error-message">{{ $message }}</div>
            @enderror

            <label for="post_code">郵便番号</label>
            <input type="text" name="post_code" id="post_code" value="{{ old('post_code', Auth::user()->post_code) }}">
            @error('post_code')
                <div class="error-message">{{ $message }}</div>
            @enderror

            <label for="address">住所</label>
            <input type="text" name="address" id="address" value="{{ old('address', Auth::user()->address) }}">
            @error('address')
                <div class="error-message">{{ $message }}</div>
            @enderror

            <label for="build">建物名</label>
            <input type="text" name="build" id="build" value="{{ old('build', Auth::user()->build) }}">
            @error('build')
                <div class="error-message">{{ $message }}</div>
            @enderror

            <button type="submit" class="update-button">更新する</button>
        </form>
    </div>
@endsection

@section('js')
    <script>
        document.querySelector('.image-input').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = function(event) {
                document.querySelector('.profile-image-preview').src = event.target.result;
            };
            reader.readAsDataURL(file);
        });
    </script>
@endsection
