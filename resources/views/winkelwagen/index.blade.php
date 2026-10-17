@extends('layouts.app')

@section('title', 'Winkelwagen | Hap &amp; Hop')

@section('content')
    <x-pagina-kop titel="Winkelwagen">
        Controleer je pakketten en pas de aantallen aan voordat je bestelt.
    </x-pagina-kop>

    @if ($regels->isEmpty())
        <p style="margin-bottom: 16px;">Je winkelwagen is leeg.</p>
        <a class="knop" href="{{ route('pakketten.index') }}">Bekijk onze pakketten</a>
    @else
        <div class="wagen-lijst">
            @foreach ($regels as $regel)
                <div class="wagen-regel">
                    <div class="wagen-info">
                        <strong>{{ $regel->pakket->naam }}</strong>
                        <span class="muted">&euro; {{ number_format($regel->pakket->prijs, 2, ',', '.') }} per pakket</span>
                    </div>

                    <form class="wagen-aantal" method="POST" action="{{ route('winkelwagen.wijzigen', $regel->pakket) }}">
                        @csrf
                        @method('PATCH')
                        <label class="sr-only" for="aantal-{{ $regel->pakket->id }}">Aantal</label>
                        <input type="number" id="aantal-{{ $regel->pakket->id }}" name="aantal"
                               min="1" max="100" value="{{ $regel->aantal }}" data-auto-verstuur>
                        <noscript><button type="submit" class="knop knop-klein knop-licht">Bijwerken</button></noscript>
                    </form>

                    <div class="wagen-prijs">&euro; {{ number_format($regel->totaal, 2, ',', '.') }}</div>

                    <form method="POST" action="{{ route('winkelwagen.verwijderen', $regel->pakket) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="link-verwijder">Verwijderen</button>
                    </form>
                </div>
            @endforeach
        </div>

        <div class="wagen-totaal">
            <span>Totaal</span>
            <span>&euro; {{ number_format($totaal, 2, ',', '.') }}</span>
        </div>

        <div class="wagen-acties">
            <a class="knop knop-licht" href="{{ route('pakketten.index') }}">Verder winkelen</a>
            <a class="knop" href="{{ route('bestellen.create') }}">Bestellen</a>
        </div>
    @endif
@endsection