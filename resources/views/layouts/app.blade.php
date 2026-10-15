<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Hap &amp; Hop')</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/extra.css') }}">
</head>
<body>
    <x-header />

    @yield('hero')

    <main class="container">
        @if (session('succes'))
            <div class="melding succes">{{ session('succes') }}</div>
        @endif

        @yield('content')
    </main>

    <x-footer />
</body>
</html>