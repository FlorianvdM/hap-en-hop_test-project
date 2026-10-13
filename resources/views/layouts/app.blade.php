<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Hap &amp; Hop')</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
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