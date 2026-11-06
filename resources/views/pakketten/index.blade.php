@extends('layouts.app')

@section('title', 'Pakketten | Hap & Hop')
@section('beschrijving', 'Lunch- en ontbijtpakketten van Hap & Hop, vers bereid in Houten. Bestel online, ophalen of laten bezorgen.')

@section('kop')
    <x-pagina-kop titel="Onze pakketten">
        Kies het pakket dat bij jou past. Al onze pakketten zijn vers bereid en met zorg samengesteld.
    </x-pagina-kop>
@endsection

@section('content')
    @if ($pakketten->isEmpty())
        <p>Er zijn nog geen pakketten.</p>
    @else
        <div class="winkel-raster">
            <div class="kaarten">
                @foreach ($pakketten as $pakket)
                    <x-pakket-kaart :pakket="$pakket" />
                @endforeach
            </div>

            <x-winkelwagen-zijbalk />
        </div>
    @endif
@endsection
