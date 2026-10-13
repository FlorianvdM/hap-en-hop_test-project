@php
    $nieuweBestellingen = \App\Models\Bestelling::where('status', 'nieuw')->count();
    $ongelezenBerichten = \App\Models\ContactBericht::where('gelezen', false)->count();
@endphp

<aside class="zijbalk">
    <nav>
        <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'actief' : '' }}">
            <x-icon name="home" :size="20" /> Dashboard
        </a>

        <a href="{{ route('admin.bestellingen') }}" class="{{ request()->routeIs('admin.bestellingen*') ? 'actief' : '' }}">
            <x-icon name="shopping-cart" :size="20" /> Bestellingen
            @if ($nieuweBestellingen > 0)
                <span class="badge">{{ $nieuweBestellingen }}</span>
            @endif
        </a>

        <a href="{{ route('admin.berichten') }}" class="{{ request()->routeIs('admin.berichten*') ? 'actief' : '' }}">
            <x-icon name="mail" :size="20" /> Berichten
            @if ($ongelezenBerichten > 0)
                <span class="badge">{{ $ongelezenBerichten }}</span>
            @endif
        </a>

        @foreach ([['pakketten', 'package', 'Pakketten'], ['producten', 'tag', 'Producten'], ['gebruikers', 'users', 'Gebruikers']] as [$slug, $icoon, $label])
            <a href="{{ route('admin.binnenkort', $slug) }}" class="{{ request()->is('admin/' . $slug) ? 'actief' : '' }}">
                <x-icon :name="$icoon" :size="20" /> {{ $label }}
            </a>
        @endforeach

        <a href="{{ route('admin.instellingen') }}" class="{{ request()->routeIs('admin.instellingen') ? 'actief' : '' }}">
            <x-icon name="settings" :size="20" /> Instellingen
        </a>
    </nav>
</aside>