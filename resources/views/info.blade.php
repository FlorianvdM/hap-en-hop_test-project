@extends('layouts.app')

@section('title', 'Info | Hap &amp; Hop')

@section('content')
    <x-pagina-kop titel="Informatie">
        Hier vind je alles wat je moet weten over onze service, bezorging en bestelling.
    </x-pagina-kop>

    <div class="info-lijst">
        <x-info-blok icon="truck" titel="Bezorgen & afhalen">
            Wij bezorgen in de regio of je kunt je bestelling zelf afhalen.
        </x-info-blok>

        <x-info-blok icon="clock" titel="Openingstijden">
            Maandag t/m vrijdag: 08.00 – 16.00<br>
            Zaterdag en zondag: Gesloten
        </x-info-blok>

        <x-info-blok icon="leaf" titel="Onze producten">
            Wij werken met verse en lokale ingrediënten waar mogelijk.
        </x-info-blok>

        <x-info-blok icon="credit-card" titel="Betalen">
            Je kunt bij ons betalen met pin, contant of op factuur (voor zakelijke klanten).
        </x-info-blok>
    </div>
@endsection