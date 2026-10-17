@extends('layouts.app')

@section('title', 'Producten | Hap &amp; Hop')

@section('content')
    <x-pagina-kop titel="Onze producten">
        Dit is wat we maken. Wil je iets bestellen of heb je een vraag? Bekijk onze pakketten of neem contact met ons op.
    </x-pagina-kop>

    @if ($producten->isEmpty())
        <p style="margin-bottom: 16px;">Er zijn nog geen producten.</p>
    @else
        <div class="favorieten">
            @foreach ($producten as $product)
                <div class="kaart">
                    <div class="afbeelding"></div>
                    <h3>{{ $product->naam }}</h3>
                    <p class="muted">{{ $product->beschrijving }}</p>
                    <p class="prijs">Vanaf &euro; {{ number_format($product->prijs, 2, ',', '.') }}</p>
                    @if ($product->voorraadTekst())
                        <p class="voorraad {{ $product->uitverkocht() ? 'voorraad-op' : '' }}">{{ $product->voorraadTekst() }}</p>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

    <div class="wagen-acties" style="margin-top: 32px;">
        <a class="knop" href="{{ route('pakketten.index') }}">Bekijk de pakketten</a>
        <a class="knop knop-licht" href="{{ route('contact') }}">Neem contact op</a>
    </div>
@endsection
