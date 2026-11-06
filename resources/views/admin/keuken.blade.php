@extends('layouts.admin')

@section('title', 'Keuken | Hap & Hop')
@section('paginatitel', 'Keuken')
@section('subtitel', 'Openstaande bestellingen per gewenste datum, met het totaal per pakket of broodje, de dieetwensen en de bezorglijst. Zet een bestelling op Klaar als hij opgehaald of bezorgd kan worden.')

@section('acties')
    @if ($datum)
        <a class="knop knop-licht" href="{{ route('admin.keuken') }}">Alle dagen</a>
    @endif
    <button type="button" class="knop" onclick="window.print()">Afdrukken</button>
@endsection

@section('content')
    @forelse ($dagen as $dag)
        <section class="dag">
            <div class="dag-kop">
                <h2>{{ ucfirst($dag['datum']->locale('nl')->translatedFormat('l j F Y')) }}</h2>
                @unless ($datum)
                    <a class="knop knop-klein knop-licht geen-print" href="{{ route('admin.keuken', ['datum' => $dag['datum']->toDateString()]) }}">Alleen deze dag</a>
                @endunless
            </div>

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
                            <th class="geen-print">Status</th>
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
                                <td data-label="Status" class="geen-print">
                                    <form method="POST" action="{{ route('admin.bestellingen.status', $bestelling) }}">
                                        @csrf
                                        @method('PATCH')
                                        <select name="status" aria-label="Status van bestelling #{{ $bestelling->id }}"
                                                class="status-select status-{{ str_replace(' ', '-', $bestelling->status) }}"
                                                onchange="this.form.submit()">
                                            @foreach (\App\Models\Bestelling::KEUKENSTATUSSEN as $status)
                                                <option value="{{ $status }}" @selected($bestelling->status === $status)>
                                                    {{ $bestelling->statusNaam($status) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Bezorglijst: alle adressen van deze dag bij elkaar --}}
            @if ($dag['bezorgen']->isNotEmpty())
                <div class="bezorglijst">
                    <h3>Bezorgen ({{ $dag['bezorgen']->count() }})</h3>
                    <ol>
                        @foreach ($dag['bezorgen'] as $bestelling)
                            <li>
                                <strong>#{{ $bestelling->id }} {{ $bestelling->naam }}</strong>
                                @if ($bestelling->telefoon) &middot; {{ $bestelling->telefoon }} @endif
                                <address>{!! nl2br(e($bestelling->bezorgadres() ?? 'Geen adres opgegeven')) !!}</address>
                                <span class="muted">
                                    @foreach ($bestelling->regels as $regel)
                                        {{ $regel->aantal }}&times; {{ $regel->pakket_naam }}@unless ($loop->last), @endunless
                                    @endforeach
                                </span>
                            </li>
                        @endforeach
                    </ol>
                </div>
            @endif
        </section>
    @empty
        <p>{{ $datum ? 'Er zijn geen openstaande bestellingen voor deze dag.' : 'Er zijn geen openstaande bestellingen.' }}</p>
    @endforelse
@endsection
