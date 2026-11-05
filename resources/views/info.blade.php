@extends('layouts.app')

@section('title', 'Info | Hap & Hop')

@section('kop')
    <x-pagina-kop titel="Informatie">
        Hier vind je alles wat je moet weten over onze service, bezorging en bestelling.
    </x-pagina-kop>
@endsection

@section('content')
    <div class="info-raster">
        <x-info-blok icon="clock" titel="Openingstijd">
            Elke donderdag verse broodjes: voor 10.30 uur besteld, vanaf 12.00 uur ophalen of bezorgen.<br>
            Catering op andere dagen is ook mogelijk!
        </x-info-blok>

        <x-info-blok icon="truck" titel="Bezorgen & afhalen">
            Ophalen kan op {{ config('hapenhop.adres') }}. Wij bezorgen ook in Houten en omgeving.
        </x-info-blok>

        <x-info-blok icon="leaf" titel="Onze producten">
            Alles wordt vers en met veel aandacht bereid.
        </x-info-blok>

        <x-info-blok icon="credit-card" titel="Betalen">
            Je kunt bij ons betalen met pin, contant of op factuur (voor zakelijke klanten).
        </x-info-blok>
    </div>
@endsection
