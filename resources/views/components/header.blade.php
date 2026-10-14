@php
    $winkelwagenAantal = \App\Support\Winkelwagen::aantalStuks();
@endphp

<header class="site-header">
    <div class="header-inner">
        <a href="{{ route('home') }}" class="logo">
            <img src="{{ asset('images/logo.png') }}" alt="Hap &amp; Hop" height="56">
        </a>

        <nav class="hoofdmenu">
            <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'actief' : '' }}">Home</a>
            <a href="{{ route('pakketten.index') }}" class="{{ request()->routeIs('pakketten.*') ? 'actief' : '' }}">Pakketten</a>
            <a href="{{ route('info') }}" class="{{ request()->routeIs('info') ? 'actief' : '' }}">Info</a>
            <a href="{{ route('contact') }}" class="{{ request()->routeIs('contact') ? 'actief' : '' }}">Contact</a>
        </nav>

        <div class="header-acties">
            @auth
                @if (auth()->user()->is_admin)
                    <a href="{{ route('admin.dashboard') }}">Beheer</a>
                @endif
                <a href="{{ route('logout.confirm') }}">Uitloggen</a>
            @else
                <a href="{{ route('login') }}">Inloggen</a>
            @endauth

            <a href="{{ route('winkelwagen.index') }}" class="winkelwagen" aria-label="Winkelwagen ({{ $winkelwagenAantal }})">
                <x-icon name="shopping-cart" />
                @if ($winkelwagenAantal > 0)
                    <span class="wagen-teller">{{ $winkelwagenAantal }}</span>
                @endif
            </a>
        </div>
    </div>
</header>