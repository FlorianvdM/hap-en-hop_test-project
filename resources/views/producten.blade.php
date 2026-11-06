@extends('layouts.app')

@section('title', 'Broodjes | Hap & Hop')
@section('beschrijving', 'Verse broodjes van Hap & Hop in Houten, elke donderdag. Bestel voor 10.30 uur, vanaf 12.00 uur ophalen of bezorgen.')

@section('kop')
    <x-pagina-kop titel="Onze broodjes">
        Vers belegd en afwisselend: kijk regelmatig wat er deze week is. Leg je broodjes in de winkelwagen,
        bekijk onze pakketten of neem contact met ons op.
    </x-pagina-kop>
@endsection

@section('content')
    @if ($producten->isEmpty())
        <p>Er zijn nog geen broodjes.</p>
    @else
        <div class="kaarten">
            @foreach ($producten as $product)
                <x-product-kaart :product="$product" />
            @endforeach
        </div>
    @endif

    <div class="hero-knoppen" style="margin-top: 48px;">
        <a class="knop" href="{{ route('pakketten.index') }}">Bekijk de pakketten</a>
        <a class="knop knop-licht" href="{{ route('winkelwagen.index') }}">Naar de winkelwagen</a>
    </div>
@endsection
