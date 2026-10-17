<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Beheer | Hap &amp; Hop')</title>
    <x-favicon />
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/extra.css') }}">
    <link rel="stylesheet" href="{{ asset('css/beheer.css') }}">
    <script src="{{ asset('js/site.js') }}" defer></script>
</head>
<body>
    <x-admin.header />

    @if (\App\Support\Onderhoud::aan())
        <div style="background: #fde68a; color: #78350f; padding: 10px 16px; text-align: center; font-weight: 700; border-bottom: 1px solid #e9c46a;">
            Onderhoudsmodus staat aan: bezoekers zien een 404-pagina.
            <a href="{{ route('admin.instellingen') }}" style="color: inherit;">Zet hem uit bij Instellingen</a>
        </div>
    @endif

    <div class="admin-body">
        @unless ($__env->hasSection('zonder_zijbalk'))
            <x-admin.sidebar />
        @endunless

        <main class="admin-inhoud">
            @if (session('succes'))
                <div class="melding succes" data-verdwijnt="4000">{{ session('succes') }}</div>
            @endif

            @yield('content')
        </main>
    </div>

    <x-footer />

    <x-melding-dialoog />
</body>
</html>