@php
    // De tellers naast Bestellingen en Berichten
    $nieuweBestellingen = \App\Models\Bestelling::where('status', 'nieuw')->count();
    $ongelezenBerichten = \App\Models\ContactBericht::where('gelezen', false)->count();
@endphp

<aside class="zijbalk">
    <a href="{{ route('admin.dashboard') }}" class="merk">
        <img src="{{ asset('images/logo-merk.png') }}" alt="" height="46">
        <span>Hap &amp; Hop</span>
    </a>

    <nav aria-label="Beheer">
        <p class="groep">Dagelijks</p>

        <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'actief' : '' }}">
            <x-icon name="home" :size="20" /> Dashboard
        </a>

        <a href="{{ route('admin.bestellingen') }}" class="{{ request()->routeIs('admin.bestellingen*') ? 'actief' : '' }}">
            <x-icon name="shopping-cart" :size="20" /> Bestellingen
            @if ($nieuweBestellingen > 0)
                <span class="badge">{{ $nieuweBestellingen }}</span>
            @endif
        </a>

        <a href="{{ route('admin.keuken') }}" class="{{ request()->routeIs('admin.keuken') ? 'actief' : '' }}">
            <x-icon name="utensils" :size="20" /> Keuken
        </a>

        <a href="{{ route('admin.berichten') }}" class="{{ request()->routeIs('admin.berichten*') ? 'actief' : '' }}">
            <x-icon name="mail" :size="20" /> Berichten
            @if ($ongelezenBerichten > 0)
                <span class="badge">{{ $ongelezenBerichten }}</span>
            @endif
        </a>

        <p class="groep">Aanbod en website</p>

        <a href="{{ route('admin.pakketten.index') }}" class="{{ request()->routeIs('admin.pakketten*') ? 'actief' : '' }}">
            <x-icon name="package" :size="20" /> Pakketten
        </a>

        <a href="{{ route('admin.producten.index') }}" class="{{ request()->routeIs('admin.producten*') ? 'actief' : '' }}">
            <x-icon name="tag" :size="20" /> Producten
        </a>

        <a href="{{ route('admin.reviews.index') }}" class="{{ request()->routeIs('admin.reviews*') ? 'actief' : '' }}">
            <x-icon name="star" :size="20" /> Reviews
        </a>

        <a href="{{ route('admin.team.index') }}" class="{{ request()->routeIs('admin.team*') ? 'actief' : '' }}">
            <x-icon name="user" :size="20" /> Wie zijn wij
        </a>

        <p class="groep">Beheer</p>

        <a href="{{ route('admin.gebruikers.index') }}" class="{{ request()->routeIs('admin.gebruikers*') ? 'actief' : '' }}">
            <x-icon name="users" :size="20" /> Gebruikers
        </a>

        <a href="{{ route('admin.instellingen') }}" class="{{ request()->routeIs('admin.instellingen') ? 'actief' : '' }}">
            <x-icon name="settings" :size="20" /> Instellingen
        </a>
    </nav>

    <div class="zijbalk-voet">
        <strong>{{ auth()->user()->name }}</strong>
        <span>{{ auth()->user()->email }}</span>
        <div>
            <a href="{{ route('home') }}"><x-icon name="home" :size="18" /> Website</a>
            <a href="{{ route('logout.confirm') }}"><x-icon name="log-out" :size="18" /> Uitloggen</a>
        </div>
    </div>
</aside>
