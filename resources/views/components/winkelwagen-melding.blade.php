{{-- Blokje onder de winkelwagenknop na "In winkelwagen": fadet in, blijft even staan en fadet weer uit (zie public/js/site.js) --}}
@props(['naam'])

@php
    $regels = \App\Support\Winkelwagen::regels();
@endphp

<div class="wagen-melding" id="wagen-melding" role="status">
    <div class="wagen-melding-kop">
        <strong><span aria-hidden="true">&#10003;</span> {{ $naam }} toegevoegd</strong>
        <button type="button" class="wagen-melding-sluit" aria-label="Sluiten">&times;</button>
    </div>

    <ul>
        @foreach ($regels as $regel)
            <li>
                <span>{{ $regel->aantal }}&times; {{ $regel->item->naam }}</span>
                <span>&euro; {{ number_format($regel->totaal, 2, ',', '.') }}</span>
            </li>
        @endforeach
    </ul>

    <div class="wagen-melding-totaal">
        <span>Totaal</span>
        <strong>&euro; {{ number_format(\App\Support\Winkelwagen::totaal($regels), 2, ',', '.') }}</strong>
    </div>

    <div class="wagen-melding-acties">
        <a class="knop knop-licht knop-klein" href="{{ route('winkelwagen.index') }}">Winkelwagen</a>
        <a class="knop knop-klein" href="{{ route('bestellen.create') }}">Bestellen</a>
    </div>
</div>
