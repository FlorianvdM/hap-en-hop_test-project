<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Beheer | Hap &amp; Hop')</title>
    <x-favicon />

    {{-- Hetzelfde stijlblad als de website --}}
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="preload" href="{{ asset('fonts/figtree-latin-400-normal.woff2') }}" as="font" type="font/woff2" crossorigin>
    <script src="{{ asset('js/site.js') }}" defer></script>
</head>
<body>
    {{-- Staat de onderhoudsmodus aan, dan zien alleen beheerders de website --}}
    @if (\App\Support\Onderhoud::aan())
        <div class="onderhoud">
            Onderhoudsmodus staat aan: bezoekers zien een 404-pagina.
            <a href="{{ route('admin.instellingen') }}" style="color: inherit;">Zet hem uit bij Instellingen</a>
        </div>
    @endif

    @if ($__env->hasSection('zonder_zijbalk'))
        {{-- Pagina's zonder menu, zoals "Uitloggen" --}}
        <div class="beheer-los">
            <header class="site-header">
                <div class="header-inner">
                    <a href="{{ route('admin.dashboard') }}" class="merk">
                        <img src="{{ asset('images/logo-merk.png') }}" alt="" height="56">
                        <span>Hap &amp; Hop</span>
                    </a>
                    <div class="header-acties">
                        <div class="account-links">
                            <a href="{{ route('admin.dashboard') }}">Naar het beheer</a>
                        </div>
                    </div>
                </div>
            </header>

            <main class="beheer-inhoud">
                @if (session('succes'))
                    <div class="melding succes" data-verdwijnt="4000" style="margin: 0;">{{ session('succes') }}</div>
                @endif

                @yield('content')
            </main>
        </div>
    @else
        <div class="beheer">
            <x-admin.sidebar />

            <div class="beheer-hoofd">
                {{-- Balk bovenaan: titel, uitleg en knoppen van de pagina --}}
                <header class="beheer-balk">
                    <div>
                        <h1>@yield('paginatitel')</h1>
                        @hasSection('subtitel')
                            <p>@yield('subtitel')</p>
                        @endif
                    </div>

                    @hasSection('acties')
                        <div class="acties">@yield('acties')</div>
                    @endif
                </header>

                <main class="beheer-inhoud">
                    @if (session('succes'))
                        <div class="melding succes" data-verdwijnt="4000" style="margin: 0;">{{ session('succes') }}</div>
                    @endif

                    @yield('content')
                </main>
            </div>
        </div>
    @endif

    <x-melding-dialoog />
</body>
</html>
