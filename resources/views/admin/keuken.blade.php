@extends('layouts.admin')

@section('title', 'Keuken | Hap & Hop')
@section('paginatitel', 'Keuken')
@section('subtitel', 'Openstaande bestellingen (status Nieuw of In behandeling) per gewenste datum, met het totaal per pakket en de dieetwensen.')

@section('content')
    @forelse ($dagen as $dag)
        <section class="dag">
            <h2>{{ $dag['datum']->format('d-m-Y') }}</h2>

            <div>
                <p class="muted" style="font-size: 14px; margin-bottom: 10px;">Totaal te maken</p>
                <ul class="maken">
                    @foreach ($dag['pakketten'] as $naam => $aantal)
                        <li><strong>{{ $aantal }}&times;</strong> {{ $naam }}</li>
                    @endforeach
                </ul>
            </div>

            <div class="tabel-wrap">
                <table class="tabel-kaarten">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Klant</th>
                            <th>Bestelling</th>
                            <th>Levering</th>
                            <th>Dieetwensen</th>
                            <th>Opmerking</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($dag['bestellingen'] as $bestelling)
                            <tr>
                                <td data-label="#">{{ $bestelling->id }}</td>
                                <td data-label="Klant">{{ $bestelling->naam }}</td>
                                <td data-label="Bestelling">
                                    <div>
                                        @foreach ($bestelling->regels as $regel)
                                            {{ $regel->aantal }}&times; {{ $regel->pakket_naam }}<br>
                                        @endforeach
                                    </div>
                                </td>
                                <td data-label="Levering"><div>@include('admin.partials.levering', ['bestelling' => $bestelling])</div></td>
                                <td data-label="Dieetwensen">
                                    <div>
                                        @forelse (($bestelling->dieetwensen ?? []) as $wens)
                                            <span class="status dieet-label">{{ ucfirst($wens) }}</span>
                                        @empty
                                            -
                                        @endforelse
                                    </div>
                                </td>
                                <td data-label="Opmerking">{{ $bestelling->opmerking ?: '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    @empty
        <p>Er zijn geen openstaande bestellingen.</p>
    @endforelse
@endsection
