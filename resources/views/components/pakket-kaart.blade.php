@props(['pakket'])

@php
    // Elke regel van de beschrijving wordt een vinkje in de lijst
    $regels = array_filter(array_map('trim', preg_split('/\R/', (string) $pakket->beschrijving)));
@endphp

<article class="kaart">
    {{-- Zonder foto tonen we een nette placeholder --}}
    @if ($pakket->afbeeldingUrl())
        <div class="afbeelding" role="img" aria-label="{{ $pakket->naam }}"
            style="background-image: url('{{ $pakket->afbeeldingUrl() }}')"></div>
    @else
        <div class="afbeelding"><x-icon name="utensils" :size="56" /></div>
    @endif

    <div class="kaart-body">
        <h3>{{ $pakket->naam }}</h3>

        @if ($regels)
            <ul class="inhoud">
                @foreach ($regels as $regel)
                    <li>{{ $regel }}</li>
                @endforeach
            </ul>
        @endif

        @if ($pakket->allergenen)
            <p class="allergenen"><strong>Allergenen:</strong> {{ $pakket->allergenen }}</p>
        @endif

        @if ($pakket->voorraadTekst())
            <p class="voorraad {{ $pakket->uitverkocht() ? 'voorraad-op' : '' }}">{{ $pakket->voorraadTekst() }}</p>
        @endif

        <div class="kaart-voet">
            <span class="prijs">&euro; {{ number_format($pakket->prijs, 2, ',', '.') }}</span>

            <form method="POST" action="{{ route('winkelwagen.toevoegen', $pakket) }}">
                @csrf
                <button type="submit" class="knop" @disabled($pakket->uitverkocht())>
                    {{ $pakket->uitverkocht() ? 'Uitverkocht' : 'In winkelwagen' }}
                </button>
            </form>
        </div>
    </div>
</article>
