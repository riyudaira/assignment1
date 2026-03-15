@extends('layouts.layout')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/chat.css') }}">
@endsection

@section('content')
    <div class="chat-page-container">
        <aside class="chat-sidebar">
            <input type="checkbox" id="sidebar-toggle" class="sidebar-checkbox">
            <label for="sidebar-toggle" class="sidebar-label">
                <p class="sidebar-title">その他の取引</p>
            </label>
            <div class="other-items-list">
                @foreach ($otherItems as $other)
                    <a href="{{ route('chat.show', $other->id) }}" class="other-item-link">
                        {{ $other->name }}
                    </a>
                @endforeach
            </div>
        </aside>
        <main class="chat-main">
            <header class="chat-header">
                @php
                    $partner = Auth::id() === $item->user_id ? $item->buyer : $item->user;
                @endphp
                <img src="{{ $partner->profile_image ? asset('storage/' . $partner->profile_image) : asset('images/img/logo/noImage.svg') }}"
                    alt="取引相手の画像" class="header-user-icon">
                <h2 class="header-title">{{ $partner->name ?? 'ユーザー' }}さんとの取引画面</h2>
                @if (Auth::id() === $item->buyer_id)
                    <form action="{{ route('purchase.complete', $item->id) }}" method="POST" class="header-action-form">
                        @csrf
                        <button type="submit" class="btn-complete">取引を完了する</button>
                    </form>
                @endif
            </header>
            <section class="chat-item-info">
                <div class="item-img-box">
                    <img src="{{ asset($item->image_path) }}" alt="{{ $item->name }}">
                </div>
                <div class="item-text-box">
                    <h2 class="chat-item-name">{{ $item->name }}</h2>
                    <p class="chat-item-price">¥{{ number_format($item->price) }}</p>
                </div>
            </section>
            <div class="message-container">
                @foreach ($messages as $message)
                    @if ($message->user_id !== Auth::id())
                        <div class="message-row partner">
                            <div class="message-content">
                                <img src="{{ $message->user->profile_image
                                    ? asset('storage/' . $message->user->profile_image)
                                    : asset('images/img/logo/noImage.svg') }}"
                                    alt="相手の画像" class="chat-user-icon">
                                <span class="sender-name">{{ $message->user->name }}</span>
                            </div>
                            <div class="message-content">
                                <div class="bubble">
                                    @if ($message->image_path)
                                        <div class="chat-sent-image">
                                            <img src="{{ asset('storage/' . $message->image_path) }}" alt="送信画像">
                                        </div>
                                    @endif
                                    @if ($message->text)
                                        <span class="text-content"
                                            style="white-space: pre-wrap;">{{ $message->text }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="message-row self" id="message-{{ $message->id }}">
                            <div class="message-content">
                                <span class="sender-name">{{ Auth::user()->name }}</span>
                                <img src="{{ Auth::user()->profile_image ? asset('storage/' . Auth::user()->profile_image) : asset('images/img/logo/noImage.svg') }}"
                                    alt="自分の画像" class="chat-user-icon">
                            </div>
                            <div>
                                <div class="bubble display-area">
                                    @if ($message->image_path)
                                        <div class="chat-sent-image">
                                            <img src="{{ asset('storage/' . $message->image_path) }}" alt="送信画像">
                                        </div>
                                    @endif
                                    @if ($message->text)
                                        <span class="text-content"
                                            style="white-space: pre-wrap;">{{ $message->text }}</span>
                                    @endif
                                    @if ($message->is_edited)
                                        <small style="font-size: 10px; color: #717171; margin-left: 5px;">(編集済)</small>
                                    @endif
                                </div>
                                <div class="edit-area" style="display: none;">
                                    <form action="{{ route('chat.update', $message->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <textarea name="message" class="edit-textarea" required>{{ $message->text }}</textarea>
                                        <div class="edit-buttons">
                                            <button type="button" class="btn-cancel"
                                                onclick="toggleEdit({{ $message->id }}, false)">キャンセル</button>
                                            <button type="submit" class="btn-save">保存</button>
                                        </div>
                                    </form>
                                </div>
                                <div class="message-footer">
                                    <button class="msg-action" onclick="toggleEdit({{ $message->id }}, true)">編集</button>
                                    <form action="{{ route('chat.destroy', $message->id) }}" method="POST"
                                        onsubmit="return confirm('本当に削除しますか？')" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="msg-action">削除</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
            @if ($errors->any())
                <div class="chat-error-container" style="padding: 0 40px;">
                    <ul
                        style="list-style: none; background: #ffebeb; color: #ff5555; padding: 10px; border-radius: 4px; font-size: 14px; margin-bottom: 10px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <footer class="chat-footer">
                <form action="{{ route('chat.store', $item->id) }}" method="POST" id="chat-form" class="message-form"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="message-input-container">
                        <div id="image-preview-wrapper" class="preview-wrapper" style="display: none;">
                            <img id="image-preview" src="" alt="プレビュー">
                            <button type="button" class="preview-clear-btn" onclick="clearImage()">✕</button>
                        </div>
                        <textarea name="message" id="message-textarea" placeholder="取引メッセージを記入してください" oninput="saveDraft()"
                            class="message-textarea"></textarea>
                    </div>
                    <input type="file" name="image" id="image-input" style="display: none;"
                        accept="image/png, image/jpeg" onchange="previewImage(this)">
                    <button type="button" class="btn-add-img"
                        onclick="document.getElementById('image-input').click()">画像を追加</button>
                    <button type="submit" class="btn-send">
                        <img src="{{ asset('images/img/logo/submitbuttun.svg') }}" alt="送信">
                    </button>
                </form>
            </footer>
        </main>
    </div>
    @if ($showModal)
        <div class="modal-overlay">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">取引が完了しました。</h3>
                </div>
                <form action="{{ route('items.review.store', $item->id) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <p class="review-label">今回の取引相手はどうでしたか？</p>
                        <div class="star-rating-container">
                            <div class="star-rating">
                                <input type="radio" id="star5" name="rating" value="5" required><label
                                    for="star5">★</label>
                                <input type="radio" id="star4" name="rating" value="4"><label
                                    for="star4">★</label>
                                <input type="radio" id="star3" name="rating" value="3"><label
                                    for="star3">★</label>
                                <input type="radio" id="star2" name="rating" value="2"><label
                                    for="star2">★</label>
                                <input type="radio" id="star1" name="rating" value="1"><label
                                    for="star1">★</label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn-review-submit">送信する</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
    <form id="edit-form" method="POST" style="display: none;">
        @csrf
        @method('PATCH')
        <input type="hidden" name="message" id="edit-input">
    </form>
    <script>
        const itemId = "{{ $item->id }}";
        const storageKey = `chat_draft_${itemId}`;
        const textarea = document.getElementById('message-textarea');
        const chatForm = document.getElementById('chat-form');
        window.addEventListener('DOMContentLoaded', () => {
            const savedData = localStorage.getItem(storageKey);
            if (savedData) {
                textarea.value = savedData;
            }
            if (chatForm) {
                chatForm.addEventListener('submit', () => {
                    localStorage.removeItem(storageKey);
                });
            }
            const container = document.querySelector('.message-container');
            if (container) {
                container.scrollTop = container.scrollHeight;
            }
        });

        function saveDraft() {
            localStorage.setItem(storageKey, textarea.value);
        }

        function toggleEdit(id, isEdit) {
            const row = document.getElementById(`message-${id}`);
            const displayArea = row.querySelector('.display-area');
            const editArea = row.querySelector('.edit-area');
            const footer = row.querySelector('.message-footer');
            if (isEdit) {
                displayArea.style.display = 'none';
                footer.style.display = 'none';
                editArea.style.display = 'block';
                editArea.querySelector('textarea').focus();
            } else {
                displayArea.style.display = 'block';
                footer.style.display = 'flex';
                editArea.style.display = 'none';
            }
        }

        function previewImage(input) {
            const wrapper = document.getElementById('image-preview-wrapper');
            const preview = document.getElementById('image-preview');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    wrapper.style.display = 'block';
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        function clearImage() {
            const input = document.getElementById('image-input');
            const wrapper = document.getElementById('image-preview-wrapper');
            const preview = document.getElementById('image-preview');
            input.value = "";
            preview.src = "";
            wrapper.style.display = 'none';
        }
    </script>
@endsection
