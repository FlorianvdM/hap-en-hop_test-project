@props(['product', 'compact' => false])

<article class="kaart">
    {{-- Zonder foto tonen we een nette placeholder --}}
    @if ($product->afbeeldingUrl())
        <div class="afbeelding" role="img" aria-label="{{ $product->naam }}"
            style="background-image: url('{{ $product->afbeeldingUrl() }}')@if ($compact); height: 150px @endif"></div>
    @else
        <div class="afbeelding" @if ($compact) style="height: 150px" @endif><x-icon name="utensils" :size="56" /></div>
    @endif

    <div class="kaart-body">
        <h3>{{ $product->naam }}</h3>
        <p class="muted">{{ $product->beschrijving }}</p>

        @if ($product->voorraadTekst())
            <p class="voorraad {{ $product->uitverkocht() ? 'voorraad-op' : '' }}">{{ $product->voorraadTekst() }}</p>
        @endif

        <div class="kaart-voet">
            {{-- Alleen bestelbare producten tonen een prijs; bij catering spreek je de prijs samen af --}}
            @if ($product->bestelbaar)
                <span class="prijs">&euro; {{ number_format($product->prijs, 2, ',', '.') }}</span>
                <form method="POST" action="{{ route('winkelwagen.product.toevoegen', $product) }}">
                    @csrf
                    <button type="submit" class="knop" @disabled($product->uitverkocht())>
                        {{ $product->uitverkocht() ? 'Uitverkocht' : 'In winkelwagen' }}
                    </button>
                </form>
            @else
                <a class="knop knop-licht" href="{{ route('contact') }}">Neem contact op</a>
            @endif
        </div>
    </div>
</article>
