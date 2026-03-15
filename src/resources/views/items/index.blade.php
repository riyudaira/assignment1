@extends('layouts.layout')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('content')
    @php
        $keyword = request('keyword');
    @endphp
    <div class="tab-bar">
        <a href="{{ route('items.index', ['keyword' => $keyword]) }}"
            class="{{ request('tab') !== 'mylist' ? 'active' : '' }}">おすすめ</a>
        <a href="{{ route('items.index', ['tab' => 'mylist', 'keyword' => $keyword]) }}"
            class="{{ request('tab') === 'mylist' ? 'active' : '' }}">マイリスト</a>
    </div>
    <div class="item-grid {{ request('tab') === 'mylist' ? 'mylist-grid' : '' }}">
        @forelse ($items as $item)
            <a href="{{ route('items.detail', $item->id) }}" class="item-card-link">
                <div class="item-card">
                    <img src="{{ $item->image_path }}" alt="{{ $item->name }}" class="item-image">
                    <div class="item-name">{{ $item->name }}</div>
                    @if ($item->isSold())
                        <span class="sold-label">sold</span>
                    @endif
                </div>
            </a>
        @empty
            @if (session('message'))
                <div class="alert-message">
                    {{ session('message') }}
                </div>
            @endif
        @endforelse
    </div>
@endsection
