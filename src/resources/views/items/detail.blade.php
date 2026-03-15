@extends('layouts.layout')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/detail.css') }}">
@endsection

@section('content')
    <div class="item-detail-container">
        <div class="item-main">
            <div class="item-main-img">
                <img src="{{ asset($item->image_path) }}" alt="{{ $item->name }}" class="item-image">
            </div>
            <div class="item-info">
                <h1 class="item-name">{{ $item->name }}</h1>
                <p class="item-brand">{{ $item->brand ?? 'なし' }}</p>
                <p class="item-price">¥{{ number_format($item->price) }}（税込）</p>
                <div class="item-icon-container">
                    <div class="icon-like">
                        <span class="like-button {{ $item->likes->contains('user_id', Auth::id()) ? 'liked' : '' }}"
                            data-item-id="{{ $item->id }}"></span>
                        <p class="likes-count">{{ $item->likes->count() }}</p>
                    </div>
                    <div class="icon-comment">
                        <img src="{{ asset('images/img/logo/comment.svg') }}" alt="likes">
                        <p class="likes-count">{{ $item->comments->count() }}</p>
                    </div>
                </div>
                <div class="icon-like">
                    @auth
                        <p class="likes-count"></p>
                    @else
                        <p class="like-login-message">※いいねアイコンはログイン後に押せます</p>
                    @endauth
                </div>
                @auth
                    @if ($item->user_id === Auth::id())
                        <div class="purchase-button seller-disabled">出品した商品です</div>
                    @elseif ($item->isSold())
                        <div class="purchase-button sold-disabled">購入済みの商品です</div>
                    @else
                        <a href="{{ route('purchase.show', ['item' => $item->id]) }}" class="purchase-button">購入手続きへ</a>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="purchase-button">ログインして購入手続きへ</a>
                @endauth
                <div class="item-description">
                    <h3>商品説明</h3>
                    <p>{{ $item->description }}</p>
                </div>
                <div class="item-meta">
                    <h3>商品の情報</h3>
                    <ul class="list-container">
                        <li class="item-list">カテゴリー
                            @forelse ($item->categories ?? [] as $category)
                                <span class="category-label">{{ $category->name }}</span>
                            @empty
                                <span class="category-label">未分類</span>
                            @endforelse
                        </li>
                        <li class="item-list">商品の状態 <span class="condition-label">{{ $item->condition }}</span></li>
                    </ul>
                </div>
                <div class="item-comments">
                    <h3>コメント ({{ $item->comments->count() }})</h3>
                    @foreach ($item->comments as $comment)
                        <div class="comment-item">
                            <div class="comment-header">
                                <img src="{{ $comment->user->profile_image
                                    ? asset('storage/' . $comment->user->profile_image)
                                    : asset('images/img/logo/noImage.svg') }}"
                                    alt="{{ $comment->user->name ?? '匿名' }}のプロフィール画像" class="comment-user-image">
                                <strong>{{ $comment->user->name ?? '匿名' }}</strong>
                            </div>
                            <p class="comment-content">{{ $comment->content }}</p>
                        </div>
                    @endforeach
                    @auth
                        <form method="POST" action="{{ route('comments.store', $item->id) }}">
                            @csrf
                            <textarea class="detail-textbox" name="content" placeholder="こちらにコメントを入力"></textarea>
                            @error('content')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                            <button type="submit" class="comment-button">コメントを送信する</button>
                        </form>
                    @else
                        <p>コメントするにはログインしてください。</p>
                    @endauth
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        document.querySelector('.like-button').addEventListener('click', function() {
            const itemId = this.dataset.itemId;
            const button = this;
            fetch(`/item/${itemId}/like`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({})
                })
                .then(response => response.json())
                .then(data => {
                    document.querySelector('.likes-count').textContent = data.likes_count;
                    button.classList.toggle('liked');
                });
        });
    </script>
@endpush
