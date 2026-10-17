<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Hap &amp; Hop')</title>
    <x-favicon />
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/extra.css') }}">
    <link rel="stylesheet" href="{{ asset('css/website.css') }}">
    <link rel="preload" href="{{ asset('fonts/anton-latin-400.woff2') }}" as="font" type="font/woff2" crossorigin>
    <script src="{{ asset('js/site.js') }}" defer></script>
</head>
<body class="website">
    <x-header />

    @yield('hero')

    <main class="container">
        @if (session('succes'))
            <div class="melding succes" data-verdwijnt="4000">{{ session('succes') }}</div>
        @endif

        @yield('content')
    </main>

    <x-footer />
</body>
</html>