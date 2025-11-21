@extends('layouts.layout')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/sell.css') }}">
@endsection

@section('content')
    <div class="sell-container">
        <h1 class="sell-title">商品の出品</h1>
        <form method="POST" action="{{ route('item.store') }}" enctype="multipart/form-data" class="sell-form">
            @csrf
            {{-- 商品画像 --}}
            <p class="section-title">商品画像</p>
            <div class="item-image-wrapper">
                <div class="image-select-area">
                    <label class="image-select-btn">
                        画像を選択する
                        <input type="file" name="item_image" accept="image/*" class="image-input">
                    </label>
                </div>
                <div class="image-preview-area">
                    <img src="" alt="プレビュー画像" class="item-image-preview">
                </div>
            </div>
            @error('item_image')
                <div class="error-message">{{ $message }}</div>
            @enderror
            <hr class="section-line">

            {{-- 商品の詳細 --}}
            <p class="section-title">商品の詳細</p>

            <label class="label-text">カテゴリー</label>
            <div class="category-tags">
                @foreach ($categories as $category)
                    <label class="category-tag">
                        <input type="checkbox" name="categories[]" value="{{ $category->id }}">
                        {{ $category->name }}
                    </label>
                @endforeach
            </div>
            @error('categories')
                <div class="error-message">{{ $message }}</div>
            @enderror
            <label class="label-text">商品の状態</label>
            <select name="condition" class="select-input">
                <option disabled selected>選択してください</option>
                <option value="良好">良好</option>
                <option value="目立った傷や汚れなし">目立った傷や汚れなし</option>
                <option value="やや傷や汚れあり">やや傷や汚れあり</option>
                <option value="状態が悪い">状態が悪い</option>
            </select>
            @error('condition')
                <div class="error-message">{{ $message }}</div>
            @enderror
            <hr class="section-line">

            {{-- 商品名と説明 --}}
            <p class="section-title">商品名と説明</p>

            <label class="label-text">商品名</label>
            <input type="text" name="name" value="{{ old('name') }}" class="sell-form-input">
            @error('name')
                <div class="error-message">{{ $message }}</div>
            @enderror

            <label class="label-text">ブランド名</label>
            <input type="text" name="brand" value="{{ old('brand') }}" class="sell-form-input">
            @error('brand')
                <div class="error-message">{{ $message }}</div>
            @enderror

            <label class="label-text">商品の説明</label>
            <textarea name="description" class="textarea-input">{{ old('description') }}</textarea>
            @error('description')
                <div class="error-message">{{ $message }}</div>
            @enderror

            <label class="label-text">販売価格</label>
            <div class="price-input-wrapper">
                <input type="number" name="price" value="{{ old('price') }}" class="price-input">
            </div>
            @error('price')
                <div class="error-message">{{ $message }}</div>
            @enderror
            <button type="submit" class="sell-button">出品する</button>
        </form>
    </div>
@endsection

@push('js')
    <script>
        document.querySelector('.image-input').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;

            const preview = document.querySelector('.item-image-preview');
            const reader = new FileReader();

            reader.onload = function(event) {
                preview.src = event.target.result;
                preview.style.display = 'block';
                preview.style.opacity = 0;
                setTimeout(() => {
                    preview.style.opacity = 1;
                }, 50);
            };

            reader.readAsDataURL(file);
        });
    </script>
@endpush
