@php
    $winkelwagenAantal = \App\Support\Winkelwagen::aantalStuks();
    $overOnsActief = request()->routeIs('info', 'contact', 'wie-zijn-wij');
@endphp

<header class="site-header" id="site-header">
    <div class="header-inner">
        <a href="{{ route('home') }}" class="merk" aria-label="Hap &amp; Hop, naar de homepage">
            <img src="{{ asset('images/logo-merk.png') }}" alt="" height="56">
            <span>Hap &amp; Hop</span>
        </a>

        <nav class="hoofdmenu" id="hoofdmenu" aria-label="Hoofdmenu">
            <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'actief' : '' }}">Home</a>
            <a href="{{ route('pakketten.index') }}"
                class="{{ request()->routeIs('pakketten.*') ? 'actief' : '' }}">Pakketten</a>
            <a href="{{ route('producten') }}"
                class="{{ request()->routeIs('producten') ? 'actief' : '' }}">Broodjes</a>

            {{-- Uitklapmenu "Over ons" (open en dicht via public/js/site.js) --}}
            <div class="menu-groep">
                <button type="button" class="menu-knop {{ $overOnsActief ? 'actief' : '' }}"
                    aria-expanded="false" aria-controls="submenu-over-ons">
                    Over ons <x-icon name="chevron-down" :size="16" />
                </button>

                <div class="submenu" id="submenu-over-ons">
                    <a href="{{ route('wie-zijn-wij') }}"
                        class="{{ request()->routeIs('wie-zijn-wij') ? 'actief' : '' }}">Wie zijn wij</a>
                    <a href="{{ route('info') }}" class="{{ request()->routeIs('info') ? 'actief' : '' }}">Info</a>
                    <a href="{{ route('contact') }}"
                        class="{{ request()->routeIs('contact') ? 'actief' : '' }}">Contact</a>
                </div>
            </div>

            {{-- Op een telefoon staan de accountlinks in het uitklapmenu --}}
            <div class="menu-account">
                @auth
                    <a href="{{ route('mijn-bestellingen') }}">Mijn bestellingen</a>
                    @if (auth()->user()->is_admin)
                        <a href="{{ route('admin.dashboard') }}">Beheer</a>
                    @endif
                    <a href="{{ route('logout.confirm') }}">Uitloggen</a>
                @else
                    <a href="{{ route('login') }}">Inloggen</a>
                @endauth
            </div>
        </nav>

        <div class="header-acties">
            <div class="account-links">
                @auth
                    <a href="{{ route('mijn-bestellingen') }}">Mijn bestellingen</a>
                    @if (auth()->user()->is_admin)
                        <a href="{{ route('admin.dashboard') }}">Beheer</a>
                    @endif
                    <a href="{{ route('logout.confirm') }}">Uitloggen</a>
                @else
                    <a href="{{ route('login') }}">Inloggen</a>
                @endauth
            </div>

            <a href="{{ route('winkelwagen.index') }}" class="wagen"
                aria-label="Winkelwagen ({{ $winkelwagenAantal }})">
                <x-icon name="shopping-cart" />
                <span class="tekst">Winkelwagen</span>
                @if ($winkelwagenAantal > 0)
                    <span class="teller">{{ $winkelwagenAantal }}</span>
                @endif
            </a>

            <button type="button" class="menu-knop-mobiel" aria-controls="hoofdmenu" aria-expanded="false"
                aria-label="Menu openen">
                <x-icon name="menu" />
            </button>
        </div>
    </div>
</header>
