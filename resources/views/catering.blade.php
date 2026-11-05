@extends('layouts.app')

@section('title', 'Catering | Hap & Hop')

@section('kop')
    <x-pagina-kop titel="Catering">
        Een lunch op het werk, een verjaardag of een feest: wij verzorgen het eten, ook op andere dagen dan donderdag.
    </x-pagina-kop>
@endsection

@section('content')
    {{-- Bewust zonder prijzen: die spreken we per aanvraag af --}}
    <div class="info-raster">
        <x-info-blok icon="utensils" titel="Op maat">
            Vertel ons voor hoeveel personen en wat je wensen zijn. Wij stellen er een passend aanbod bij samen.
        </x-info-blok>

        <x-info-blok icon="clock" titel="Elke dag mogelijk">
            Onze broodjes zijn er op donderdag, maar catering kan ook op andere dagen. Vraag het op tijd aan.
        </x-info-blok>

        <x-info-blok icon="leaf" titel="Vers bereid">
            Alles wordt vers en met veel aandacht bereid. Ook met dieetwensen, zoals vegetarisch of halal, houden we rekening.
        </x-info-blok>

        <x-info-blok icon="truck" titel="Ophalen of bezorgen">
            Ophalen kan op {{ config('hapenhop.adres') }}. Wij bezorgen ook in Houten en omgeving.
        </x-info-blok>
    </div>

    @if ($producten->isNotEmpty())
        <div class="kaarten" style="margin-top: 48px;">
            @foreach ($producten as $product)
                <x-product-kaart :product="$product" />
            @endforeach
        </div>
    @endif

    <div class="hero-knoppen" style="margin-top: 48px;">
        <a class="knop" href="{{ route('contact') }}">Catering aanvragen</a>
        <a class="knop knop-licht" href="{{ route('pakketten.index') }}">Bekijk de pakketten</a>
    </div>
@endsection
