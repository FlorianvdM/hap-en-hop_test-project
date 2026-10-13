@extends('layouts.app')

@section('title', 'Pagina niet gevonden | Hap &amp; Hop')

@section('content')
    <div class="foutpagina">
        <p class="foutcode">404</p>
        <h1>Deze pagina bestaat niet</h1>
        <p class="muted">
            De pagina die je zoekt is verplaatst of bestaat niet meer. Kijk gerust even bij onze pakketten.
        </p>

        <div class="knoppen">
            <a class="knop" href="{{ route('home') }}">Naar de homepage</a>
            <a class="knop knop-licht" href="{{ route('pakketten.index') }}">Bekijk de pakketten</a>
        </div>
    </div>
@endsection