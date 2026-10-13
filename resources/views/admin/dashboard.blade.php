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
                        <th>Pakket</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($recenteBestellingen as $bestelling)
                        <tr>
                            <td>{{ $bestelling->created_at->format('d-m-Y') }}</td>
                            <td>{{ $bestelling->naam }}</td>
                            <td>{{ $bestelling->pakket->naam }}</td>
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
@endsection