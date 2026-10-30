@extends('layouts.app')

@section('title', 'Broodjes | Hap & Hop')

@section('content')
    <x-pagina-kop titel="Onze broodjes">
        Vers belegd en afwisselend: kijk regelmatig wat er deze week is. Leg je broodjes in de winkelwagen,
        bekijk onze pakketten of neem contact met ons op.
    </x-pagina-kop>

    @if ($producten->isEmpty())
        <p style="margin-bottom: 16px;">Er zijn nog geen broodjes.</p>
    @else
        <div class="favorieten">
            @foreach ($producten as $product)
                <div class="kaart">
                    <div class="afbeelding" @if ($product->afbeeldingUrl())
                    style="background-image: url('{{ $product->afbeeldingUrl() }}')" @endif></div>
                    <h3>{{ $product->naam }}</h3>
                    <p class="muted">{{ $product->beschrijving }}</p>

                    {{-- Bestelbaar = vaste prijs; anders een "Vanaf"-prijs --}}
                    <p class="prijs">{{ $product->bestelbaar ? '' : 'Vanaf ' }}&euro;
                        {{ number_format($product->prijs, 2, ',', '.') }}</p>

                    @if ($product->voorraadTekst())
                        <p class="voorraad {{ $product->uitverkocht() ? 'voorraad-op' : '' }}">{{ $product->voorraadTekst() }}</p>
                    @endif

                    {{-- De bestelknop staat alleen bij producten die bestelbaar zijn --}}
                    @if ($product->bestelbaar)
                        <form method="POST" action="{{ route('winkelwagen.product.toevoegen', $product) }}">
                            @csrf
                            <button type="submit" class="knop" @disabled($product->uitverkocht())>
                                {{ $product->uitverkocht() ? 'Uitverkocht' : 'In winkelwagen' }}
                            </button>
                        </form>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

    <div class="wagen-acties" style="margin-top: 32px;">
        <a class="knop" href="{{ route('pakketten.index') }}">Bekijk de pakketten</a>
        <a class="knop knop-licht" href="{{ route('winkelwagen.index') }}">Naar de winkelwagen</a>
        <a class="knop knop-licht" href="{{ route('contact') }}">Neem contact op</a>
    </div>
@endsection