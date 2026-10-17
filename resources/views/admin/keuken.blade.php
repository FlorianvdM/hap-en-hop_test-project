@extends('layouts.admin')

@section('title', 'Keuken | Hap &amp; Hop')

@section('content')
    <h1>Keuken</h1>
    <p class="muted" style="margin-bottom: 20px;">
        Openstaande bestellingen (status Nieuw of In behandeling) per gewenste datum, met het totaal per pakket en de dieetwensen.
    </p>

    @forelse ($dagen as $dag)
        <section class="keuken-dag">
            <h2>{{ $dag['datum']->format('d-m-Y') }}</h2>

            <h3>Totaal te maken</h3>
            <ul>
                @foreach ($dag['pakketten'] as $naam => $aantal)
                    <li><strong>{{ $aantal }}&times;</strong> {{ $naam }}</li>
                @endforeach
            </ul>

            <h3>Bestellingen</h3>
            <div class="tabel-wrap">
                <table>
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
                                <td>{{ $bestelling->id }}</td>
                                <td>{{ $bestelling->naam }}</td>
                                <td>
                                    @foreach ($bestelling->regels as $regel)
                                        {{ $regel->aantal }}&times; {{ $regel->pakket_naam }}<br>
                                    @endforeach
                                </td>
                                <td>@include('admin.partials.levering', ['bestelling' => $bestelling])</td>
                                <td>
                                    @forelse (($bestelling->dieetwensen ?? []) as $wens)
                                        <span class="status dieet-label">{{ ucfirst($wens) }}</span>
                                    @empty
                                        -
                                    @endforelse
                                </td>
                                <td>{{ $bestelling->opmerking ?: '-' }}</td>
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
