<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Hap &amp; Hop')</title>
    <x-favicon />

    {{-- Korte beschrijving voor Google en als voorbeeld als de link wordt gedeeld (WhatsApp, Facebook, X) --}}
    <meta name="description" content="@yield('beschrijving', 'Verse broodjes, pakketten en catering in Houten en omgeving.')">
    <link rel="canonical" href="{{ url()->current() }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="@yield('title', 'Hap &amp; Hop')">
    <meta property="og:description" content="@yield('beschrijving', 'Verse broodjes, pakketten en catering in Houten en omgeving.')">
    <meta property="og:site_name" content="Hap &amp; Hop">
    <meta property="og:image" content="{{ asset('images/og-banner.png') }}">
    <meta name="twitter:card" content="summary_large_image">

    {{-- Eén stijlblad voor de hele website. De lettertypen staan op de eigen server (public/fonts). --}}
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="preload" href="{{ asset('fonts/figtree-latin-400-normal.woff2') }}" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="{{ asset('fonts/cormorant-garamond-latin-700-normal.woff2') }}" as="font" type="font/woff2" crossorigin>
    <script src="{{ asset('js/site.js') }}" defer></script>
</head>
<body>
    <x-header />

    <main class="hoofd">
        {{-- Pagina's kunnen een hero (homepage) en/of een paginakop over de volle breedte meegeven --}}
        @yield('hero')
        @yield('kop')

        @if (session('succes'))
            <div class="wrap">
                <div class="melding succes" data-verdwijnt="4000">{{ session('succes') }}</div>
            </div>
        @endif

        {{-- "breed" = de pagina bepaalt zelf zijn secties (homepage); anders komt de inhoud in een vaste kolom --}}
        @hasSection('breed')
            @yield('breed')
        @else
            <div class="pagina-inhoud">
                <div class="wrap">
                    @yield('content')
                </div>
            </div>
        @endif
    </main>

    <x-footer />

    {{-- Foutmeldingen (bijvoorbeeld "uitverkocht") komen in een pop-up --}}
    <x-melding-dialoog />
</body>
</html>
