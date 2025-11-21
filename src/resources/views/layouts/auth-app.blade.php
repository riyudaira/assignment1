<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COACHTECH</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inika:wght@400;700&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Noto+Sans+JP:wght@300&family=Noto+Serif:ital,wght@0,100..900;1,100..900&family=Racing+Sans+One&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/auth-app.css') }}">

    @yield('css')
</head>

<body>
    <header class="site-header">
        <div class="header-inner">
            <a href="{{ route('items.index') }}" class="logo"><img src="{{ asset('images/toppage-header-icon.svg') }}"
                    alt="COACHTECH">
            </a>
        </div>
    </header>
    <main class="site-main">
        @yield('content')
    </main>
</body>

</html>
