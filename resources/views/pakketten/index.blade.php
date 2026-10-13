@extends('layouts.app')

@section('title', 'Pakketten | Hap &amp; Hop')

@section('content')
    <h1>Onze pakketten</h1>
    <p>Kies een pakket en plaats direct je bestelling.</p>

    <div class="raster">
        @forelse ($pakketten as $pakket)
            <div class="kaart">
                <h2>{{ $pakket->naam }}</h2>
                <p>{{ $pakket->beschrijving }}</p>
                <p>Voor {{ $pakket->aantal_personen }} persoon/personen</p>
                <p class="prijs">&euro; {{ number_format($pakket->prijs, 2, ',', '.') }}</p>
                <a class="knop" href="{{ route('bestellen.create', $pakket) }}">Bestellen</a>
            </div>
        @empty
            <p>Er zijn nog geen pakketten.</p>
        @endforelse
    </div>
@endsection