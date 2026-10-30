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
            {{-- Bestelbare producten hebben een vaste prijs, de andere een "Vanaf"-prijs --}}
            <span class="prijs">
                @unless ($product->bestelbaar)<small>Vanaf</small>@endunless&euro; {{ number_format($product->prijs, 2, ',', '.') }}
            </span>

            @if ($product->bestelbaar)
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
