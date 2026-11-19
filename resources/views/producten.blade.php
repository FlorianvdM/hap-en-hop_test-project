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
    {{-- Alle broodjes als kaarten; de winkelwagen en pakketten staan al in het menu bovenaan --}}
    @if ($producten->isEmpty())
        <p>Er zijn nog geen broodjes.</p>
    @else
        <div class="kaarten">
            @foreach ($producten as $product)
                <x-product-kaart :product="$product" />
            @endforeach
        </div>
    @endif
@endsection