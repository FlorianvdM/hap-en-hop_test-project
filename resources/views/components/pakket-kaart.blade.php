@props(['pakket'])

@php
    $regels = array_filter(array_map('trim', preg_split('/\R/', (string) $pakket->beschrijving)));
@endphp

<article class="pakket-kaart">
    <div class="afbeelding"></div>

    <div class="pakket-info">
        <h2>{{ $pakket->naam }}</h2>

        @if ($regels)
            <ul>
                @foreach ($regels as $regel)
                    <li>{{ $regel }}</li>
                @endforeach
            </ul>
        @endif

        <p class="prijs">&euro; {{ number_format($pakket->prijs, 2, ',', '.') }}</p>

        <form method="POST" action="{{ route('winkelwagen.toevoegen', $pakket) }}">
            @csrf
            <button type="submit" class="knop">In winkelwagen</button>
        </form>
    </div>
</article>