@extends('layouts.admin')

@section('title', 'Dashboard | Hap & Hop')
@section('paginatitel', 'Dashboard')
@section('subtitel', 'Overzicht van je webshop')

@section('acties')
    <a class="knop knop-licht" href="{{ route('home') }}">Naar de website</a>
@endsection

@section('content')
    @php
        // Wat vraagt nu om aandacht? Dezelfde tellers als in het menu.
        $nieuweBestellingen = \App\Models\Bestelling::where('status', 'nieuw')->count();
        $ongelezenBerichten = \App\Models\ContactBericht::where('gelezen', false)->count();
    @endphp

    <div class="aandacht">
        <div class="aandacht-kaart">
            <div class="getal">{{ $nieuweBestellingen }}</div>
            <div>
                <strong>Nieuwe bestellingen</strong>
                <span>Wachten op je reactie</span>
            </div>
            <a class="knop knop-klein" href="{{ route('admin.bestellingen') }}">Bekijk</a>
        </div>

        <div class="aandacht-kaart">
            <div class="getal">{{ $ongelezenBerichten }}</div>
            <div>
                <strong>Ongelezen berichten</strong>
                <span>Uit het contactformulier</span>
            </div>
            <a class="knop knop-klein" href="{{ route('admin.berichten') }}">Lees</a>
        </div>
    </div>

    <div class="stat-raster">
        <x-stat-kaart icon="shopping-cart" label="Totaal bestellingen" :waarde="$totaalBestellingen" />
        <x-stat-kaart icon="package" label="Actieve pakketten" :waarde="$aantalPakketten" />
        <x-stat-kaart icon="user" label="Gebruikers" :waarde="$aantalGebruikers" />
        <x-stat-kaart icon="euro" label="Omzet (deze maand)" :waarde="'€ ' . number_format($omzet, 2, ',', '.')" />
    </div>

    <div>
        <div class="kop-met-knop" style="margin-bottom: 12px;">
            <h2 style="font-family: var(--tekst); font-size: 20px; letter-spacing: 0;">Omzet per maand</h2>

            {{-- Met deze knop zet je de grafiek aan of uit (onthouden in je browser) --}}
            <button type="button" class="knop knop-klein knop-licht" id="grafiek-knop"
                    aria-controls="omzet-grafiek" aria-expanded="true">
                <x-icon name="bar-chart-2" :size="16" />
                <span>Grafiek verbergen</span>
            </button>
        </div>

        <section id="omzet-grafiek" class="paneel">
            <p class="muted" style="font-size: 14px;">
                Omzet van de afgelopen 12 maanden. Een bestelling telt mee in de maand waarin hij als verzonden is verwijderd.
            </p>

            <div class="grafiek" role="img" aria-label="Staafdiagram met de omzet per maand van de afgelopen 12 maanden">
                @foreach ($grafiek['maanden'] as $maand)
                    <div class="g-kolom {{ $loop->last ? 'nu' : '' }}"
                         title="{{ $maand['maand'] }} {{ $maand['jaar'] }}: € {{ number_format($maand['omzet'], 2, ',', '.') }}">
                        <span class="g-waarde">
                            {{ $maand['omzet'] > 0 ? '€ ' . number_format($maand['omzet'], 0, ',', '.') : '' }}
                        </span>
                        <div class="g-vak">
                            <div class="g-balk" style="height: {{ $maand['hoogte'] }}%;"></div>
                        </div>
                        <span class="g-maand">{{ $maand['maand'] }}</span>
                    </div>
                @endforeach
            </div>

            <p style="margin-top: 18px;">
                Totaal afgelopen 12 maanden:
                <strong>&euro; {{ number_format($grafiek['totaal'], 2, ',', '.') }}</strong>
            </p>
        </section>
    </div>

    <section>
        <div class="kop-met-knop" style="margin-bottom: 14px;">
            <h2 style="font-family: var(--tekst); font-size: 20px; letter-spacing: 0;">Recente bestellingen</h2>
            <a class="link" href="{{ route('admin.bestellingen') }}">Alle bestellingen bekijken</a>
        </div>

        @if ($recenteBestellingen->isEmpty())
            <p class="muted">Er zijn nog geen bestellingen.</p>
        @else
            <div class="tabel-wrap">
                <table class="tabel-kaarten">
                    <thead>
                        <tr>
                            <th>Datum</th>
                            <th>Klant</th>
                            <th>Bestelling</th>
                            <th>Totaal</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recenteBestellingen as $bestelling)
                            <tr>
                                <td data-label="Datum">{{ $bestelling->created_at->format('d-m-Y') }}</td>
                                <td data-label="Klant">{{ $bestelling->naam }}</td>
                                <td data-label="Bestelling">{{ $bestelling->regels->map(fn ($regel) => $regel->aantal . '× ' . $regel->pakket_naam)->implode(', ') }}</td>
                                <td data-label="Totaal">&euro; {{ number_format($bestelling->totaal, 2, ',', '.') }}</td>
                                <td data-label="Status">
                                    <span class="status status-{{ str_replace(' ', '-', $bestelling->status) }}">
                                        {{ ucfirst($bestelling->status) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>

    <script>
        (function () {
            const knop = document.getElementById('grafiek-knop');
            const blok = document.getElementById('omzet-grafiek');
            const tekst = knop.querySelector('span');
            const opslagSleutel = 'hapenhopDashboardGrafiek';

            function zet(zichtbaar) {
                blok.hidden = !zichtbaar;
                knop.setAttribute('aria-expanded', String(zichtbaar));
                tekst.textContent = zichtbaar ? 'Grafiek verbergen' : 'Grafiek tonen';
            }

            let zichtbaar = true;

            try {
                zichtbaar = localStorage.getItem(opslagSleutel) !== 'uit';
            } catch (e) {}

            zet(zichtbaar);

            knop.addEventListener('click', function () {
                zichtbaar = !zichtbaar;
                zet(zichtbaar);

                try {
                    localStorage.setItem(opslagSleutel, zichtbaar ? 'aan' : 'uit');
                } catch (e) {}
            });
        })();
    </script>
@endsection
