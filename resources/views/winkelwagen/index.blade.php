@extends('layouts.app')

@section('title', 'Winkelwagen | Hap &amp; Hop')

@section('content')
    <x-pagina-kop titel="Winkelwagen">
        Controleer je bestelling en pas de aantallen aan voordat je bestelt.
    </x-pagina-kop>

    @if ($regels->isEmpty())
        <p style="margin-bottom: 16px;">Je winkelwagen is leeg.</p>
        <a class="knop" href="{{ route('pakketten.index') }}">Bekijk onze pakketten</a>
    @else
        <div class="wagen-lijst">
            @foreach ($regels as $regel)
                @php
                    // Een regel is een pakket of een product; de routes verschillen per soort
                    $isProduct = $regel->soort === 'product';
                    $wijzigUrl = $isProduct
                        ? route('winkelwagen.product.wijzigen', $regel->item)
                        : route('winkelwagen.wijzigen', $regel->item);
                    $verwijderUrl = $isProduct
                        ? route('winkelwagen.product.verwijderen', $regel->item)
                        : route('winkelwagen.verwijderen', $regel->item);
                    $veldId = 'aantal-' . $regel->soort . '-' . $regel->item->id;
                @endphp

                <div class="wagen-regel">
                    <div class="wagen-info">
                        <strong>{{ $regel->item->naam }}</strong>
                        <span class="muted">&euro; {{ number_format($regel->item->prijs, 2, ',', '.') }} per
                            {{ $isProduct ? 'stuk' : 'pakket' }}</span>
                    </div>

                    <form class="wagen-aantal" method="POST" action="{{ $wijzigUrl }}">
                        @csrf
                        @method('PATCH')
                        <label class="sr-only" for="{{ $veldId }}">Aantal</label>
                        <input type="number" id="{{ $veldId }}" name="aantal" min="1" max="100" value="{{ $regel->aantal }}"
                            data-auto-verstuur>
                        <noscript><button type="submit" class="knop knop-klein knop-licht">Bijwerken</button></noscript>
                    </form>

                    <div class="wagen-prijs">&euro; {{ number_format($regel->totaal, 2, ',', '.') }}</div>

                    <form method="POST" action="{{ $verwijderUrl }}">
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