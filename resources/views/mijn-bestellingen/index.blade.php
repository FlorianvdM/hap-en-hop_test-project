@extends('layouts.app')

@section('title', 'Mijn bestellingen | Hap &amp; Hop')

@section('content')
    @php
        $statusNamen = [
            'nieuw' => 'Ontvangen',
            'in behandeling' => 'In behandeling',
            'verzonden' => 'Verzonden',
        ];
    @endphp

    <x-pagina-kop titel="Mijn bestellingen">
        Hier zie je de bestellingen die je hebt geplaatst terwijl je was ingelogd.
    </x-pagina-kop>

    @forelse ($bestellingen as $bestelling)
        <article class="bestelling-kaart">
            <div class="bestelling-kop">
                <h2>Bestelling #{{ $bestelling->id }}</h2>
                <span class="status status-{{ str_replace(' ', '-', $bestelling->status) }}">
                    {{ $statusNamen[$bestelling->status] ?? ucfirst($bestelling->status) }}
                </span>
            </div>

            <p class="bestelling-meta">
                Besteld op {{ $bestelling->created_at->format('d-m-Y') }}
                &middot; Gewenste datum {{ $bestelling->gewenste_datum->format('d-m-Y') }}
            </p>

            <ul class="bestelling-regels">
                @foreach ($bestelling->regels as $regel)
                    <li>
                        <span>{{ $regel->aantal }}&times; {{ $regel->pakket_naam }}</span>
                        <span>&euro; {{ number_format($regel->aantal * $regel->prijs, 2, ',', '.') }}</span>
                    </li>
                @endforeach
            </ul>

            <div class="bestelling-totaal">
                <span>Totaal</span>
                <span>&euro; {{ number_format($bestelling->totaal, 2, ',', '.') }}</span>
            </div>

            @if (! empty($bestelling->dieetwensen))
                <p class="bestelling-extra">
                    @foreach ($bestelling->dieetwensen as $wens)
                        <span class="status dieet-label">{{ \App\Http\Controllers\BestellingController::DIEETWENSEN[$wens] ?? ucfirst($wens) }}</span>
                    @endforeach
                </p>
            @endif

            @if ($bestelling->opmerking)
                <p class="bestelling-extra muted">Je opmerking: {{ $bestelling->opmerking }}</p>
            @endif
        </article>
    @empty
        <p style="margin-bottom: 16px;">Je hebt nog geen bestellingen geplaatst terwijl je was ingelogd.</p>
        <a class="knop" href="{{ route('pakketten.index') }}">Bekijk onze pakketten</a>
    @endforelse
@endsection