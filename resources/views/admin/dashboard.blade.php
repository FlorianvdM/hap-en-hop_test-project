@extends('layouts.admin')

@section('title', 'Dashboard | Hap &amp; Hop')

@section('content')
    <h1>Dashboard</h1>

    <div class="stat-raster" style="margin-top: 20px;">
        <x-stat-kaart icon="shopping-cart" label="Totaal bestellingen" :waarde="$totaalBestellingen" />
        <x-stat-kaart icon="package" label="Actieve pakketten" :waarde="$aantalPakketten" />
        <x-stat-kaart icon="user" label="Gebruikers" :waarde="$aantalGebruikers" />
        <x-stat-kaart icon="euro" label="Omzet (deze maand)" :waarde="'€ ' . number_format($omzet, 2, ',', '.')" />
    </div>

    <div class="kop-met-knop" style="margin-top: 32px;">
        <h2 style="margin: 0;">Omzet per maand</h2>

        <button type="button" class="knop knop-klein knop-licht" id="grafiek-knop"
                aria-controls="omzet-grafiek" aria-expanded="true">
            <x-icon name="bar-chart-2" :size="16" />
            <span>Grafiek verbergen</span>
        </button>
    </div>

    <section id="omzet-grafiek" class="grafiek-blok" style="margin-top: 12px;">
        <p class="muted">
            Omzet van de afgelopen 12 maanden. Een bestelling telt mee in de maand waarin hij als verzonden is verwijderd.
        </p>

        <div class="grafiek" role="img" aria-label="Staafdiagram met de omzet per maand van de afgelopen 12 maanden">
            @foreach ($grafiek['maanden'] as $maand)
                <div class="grafiek-kolom {{ $loop->last ? 'huidig' : '' }}"
                     title="{{ $maand['maand'] }} {{ $maand['jaar'] }}: € {{ number_format($maand['omzet'], 2, ',', '.') }}">
                    <span class="grafiek-waarde">
                        {{ $maand['omzet'] > 0 ? '€ ' . number_format($maand['omzet'], 0, ',', '.') : '' }}
                    </span>
                    <div class="grafiek-balk-vak">
                        <div class="grafiek-balk" style="height: {{ $maand['hoogte'] }}%;"></div>
                    </div>
                    <span class="grafiek-maand">{{ $maand['maand'] }}</span>
                    <span class="grafiek-jaar">{{ $maand['jaar'] }}</span>
                </div>
            @endforeach
        </div>

        <p class="grafiek-totaal">
            Totaal afgelopen 12 maanden:
            <strong>&euro; {{ number_format($grafiek['totaal'], 2, ',', '.') }}</strong>
        </p>
    </section>

    <h2>Recente bestellingen</h2>

    @if ($recenteBestellingen->isEmpty())
        <p class="muted">Er zijn nog geen bestellingen.</p>
    @else
        <div class="tabel-wrap" style="margin-bottom: 12px;">
            <table>
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
                            <td>{{ $bestelling->created_at->format('d-m-Y') }}</td>
                            <td>{{ $bestelling->naam }}</td>
                            <td>{{ $bestelling->regels->map(fn ($regel) => $regel->aantal . '× ' . $regel->pakket_naam)->implode(', ') }}</td>
                            <td>&euro; {{ number_format($bestelling->totaal, 2, ',', '.') }}</td>
                            <td>
                                <span class="status status-{{ str_replace(' ', '-', $bestelling->status) }}">
                                    {{ ucfirst($bestelling->status) }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <a href="{{ route('admin.bestellingen') }}">Alle bestellingen bekijken</a>
    @endif

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