@extends('layouts.layout')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endsection

@section('content')
    <div class="profile-container">
        {{-- ユーザー情報 --}}
        <div class="profile-header">
            <div class="profile-image">
                <img src="{{ Auth::user()->profile_image ?? asset('images/user-placeholder.png') }}" alt="プロフィール画像"
                    class="profile-image-preview">
                <h2 class="username">{{ Auth::user()->name }}</h2>
            </div>
            <div class="profile-info">
                <a href="{{ route('user.profile.edit') }}" class="edit-button">プロフィールを編集</a>
            </div>
        </div>
    </div>

    {{-- タブ切り替え --}}
    <div class="tab-bar">
        <a href="{{ route('user.profile', ['tab' => 'listed']) }}"
            class="{{ request('tab') !== 'purchased' ? 'active' : '' }}">出品した商品</a>
        <a href="{{ route('user.profile', ['tab' => 'purchased']) }}"
            class="{{ request('tab') === 'purchased' ? 'active' : '' }}">購入した商品</a>
    </div>

    {{-- 商品一覧 --}}
    <div class="item-flex">
        @forelse ($items as $item)
            <div class="item-card">
                <a href="{{ route('items.detail', $item->id) }}" class="item-card-link">
                    <img src="{{ $item->image_path ? asset($item->image_path) : asset('images/item-placeholder.png') }}"
                        alt="商品画像" class="item-image">
                    <p class="item-name">{{ $item->name }}</p>
                </a>
            </div>
        @empty
            <p class="no-items">商品がありません。</p>
        @endforelse
    </div>
@endsection
