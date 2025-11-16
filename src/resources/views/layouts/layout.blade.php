<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COACHTECH</title>
    {{-- フォント inter --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inika:wght@400;700&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Noto+Sans+JP:wght@300&family=Noto+Serif:ital,wght@0,100..900;1,100..900&family=Racing+Sans+One&display=swap"
        rel="stylesheet">
    {{-- 共通CSS --}}
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">

    {{-- ページ固有CSS --}}
    @yield('css')
</head>

<body>
    <header class="site-header">
        <div class="header-inner">
            <a href="{{ route('items.index') }}" class="logo">
                <img src="{{ asset('images/toppage-header-icon.svg') }}" alt="COACHTECH">
            </a>

            <form method="GET" action="{{ route('items.index') }}" class="search-form">
                <input type="text" name="keyword" placeholder="なにをお探しですか？" value="{{ request('keyword') }}">
            </form>

            <ul class="header-nav">
                @if (Auth::check())
                    <li class="header-nav__item">
                        <form action="/logout" method="post">
                            @csrf
                            <button class="header-nav__button">ログアウト</button>
                        </form>
                    </li>
                    <li class="header-nav__item">
                        <a class="header-nav__link" href="{{ route('user.profile') }}">マイページ</a>
                    </li>
                    <li class="header-nav__item">
                        <a class="header-nav__link" href="{{ route('items.sell') }}">出品</a>
                    </li>
                @else
                    <li class="header-nav__item">
                        <a class="header-nav__link" href="{{ route('login') }}">ログイン</a>
                    </li>
                    <li class="header-nav__item">
                        <a class="header-nav__link" href="{{ route('login') }}">マイページ</a>
                    </li>
                    <li class="header-nav__item">
                        <a class="header-nav__link" href="{{ route('login') }}">出品</a>
                    </li>
                @endif
            </ul>
        </div>
    </header>


    <main class="site-main">
        @yield('content')
    </main>

    @yield('js')

</body>

</html>
