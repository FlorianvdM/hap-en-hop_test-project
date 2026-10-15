<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Beheer | Hap &amp; Hop')</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/extra.css') }}">
    <link rel="stylesheet" href="{{ asset('css/beheer.css') }}">
</head>
<body>
    <x-admin.header />

    <div class="admin-body">
        @unless ($__env->hasSection('zonder_zijbalk'))
            <x-admin.sidebar />
        @endunless

        <main class="admin-inhoud">
            @if (session('succes'))
                <div class="melding succes">{{ session('succes') }}</div>
            @endif

            @yield('content')
        </main>
    </div>

    <x-footer />

    <x-melding-dialoog />
</body>
</html>