{{-- Overzicht van de winkelwagen rechts naast de kaarten, alleen op desktop (zie .winkel-raster in style.css) --}}
@php
    $regels = \App\Support\Winkelwagen::regels();
@endphp

<aside class="samenvatting winkelwagen-zijbalk" aria-label="Je winkelwagen">
    <h2>Winkelwagen</h2>

    @if ($regels->isEmpty())
        <p class="muted">Je winkelwagen is nog leeg.</p>
    @else
        <ul>
            @foreach ($regels as $regel)
                <li>
                    <span>{{ $regel->aantal }}&times; {{ $regel->item->naam }}</span>
                    <span>&euro; {{ number_format($regel->totaal, 2, ',', '.') }}</span>
                </li>
            @endforeach
        </ul>

        <div class="totaal">
            <span>Totaal</span>
            <span>&euro; {{ number_format(\App\Support\Winkelwagen::totaal($regels), 2, ',', '.') }}</span>
        </div>

        <a class="knop knop-vol" href="{{ route('bestellen.create') }}">Bestellen</a>
        <a class="link" href="{{ route('winkelwagen.index') }}">Winkelwagen aanpassen</a>
    @endif
</aside>
