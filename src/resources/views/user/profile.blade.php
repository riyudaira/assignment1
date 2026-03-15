@extends('layouts.layout')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endsection

@section('content')
    <div class="profile-container">
        <div class="profile-header">
            <div class="profile-image">
                <img src="{{ Auth::user()->profile_image
                    ? asset('storage/' . Auth::user()->profile_image)
                    : asset('images/img/logo/noImage.svg') }}"
                    alt="プロフィール画像" class="profile-image-preview">
                <div class="user-meta">
                    <h1 class="username">{{ Auth::user()->name }}</h1>
                    @if (Auth::user()->evaluation_count > 0)
                        <div class="user-rating">
                            @php
                                $displayRating = (int) round(Auth::user()->evaluation);
                            @endphp
                            @for ($i = 1; $i <= 5; $i++)
                                @if ($i <= $displayRating)
                                    <span class="star yellow">★</span>
                                @else
                                    <span class="star gray">★</span>
                                @endif
                            @endfor
                        </div>
                    @endif
                </div>
            </div>
            <div class="profile-info">
                <a href="{{ route('user.profile.edit') }}" class="edit-button">プロフィールを編集</a>
            </div>
        </div>
    </div>
    </div>
    <div class="tab-bar">
        <a href="{{ route('user.profile', ['tab' => 'listed']) }}"
            class="{{ request('tab') === 'listed' || !request('tab') ? 'active' : '' }}">出品した商品</a>
        <a href="{{ route('user.profile', ['tab' => 'purchased']) }}"
            class="{{ request('tab') === 'purchased' ? 'active' : '' }}">購入した商品</a>
        <a href="{{ route('user.profile', ['tab' => 'dealing']) }}"
            class="{{ request('tab') === 'dealing' ? 'active' : '' }}">取引中の商品
            @if ($dealingCount > 0)
                <span class="badge">{{ $dealingCount }}</span>
            @endif
        </a>
    </div>
    <div class="item-flex">
        @forelse ($items as $item)
            <div class="item-card">
                @if (request('tab') === 'dealing')
                    @if ($item->unread_chats_count > 0)
                        <div class="message-count-badge">{{ $item->unread_chats_count }}</div>
                    @endif
                    @php
                        $isEvaluated = $item->reviews()->where('reviewer_id', Auth::id())->exists();
                    @endphp
                    @if ($isEvaluated)
                        <div class="evaluated-badge">評価済み</div>
                    @endif
                @endif
                <a href="{{ request('tab') === 'dealing' ? route('chat.show', $item->id) : route('items.detail', $item->id) }}"
                    class="item-card-link">
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
