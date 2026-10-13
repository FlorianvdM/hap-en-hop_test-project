@extends('layouts.app')

@section('title', 'Home | Hap &amp; Hop')

@section('hero')
    <section class="hero">
        <div class="hero-inhoud">
            <h1>Verse broodjes &amp; heerlijke catering</h1>
            <p>Voor elke gelegenheid, van ontbijt tot lunch.</p>
            <a class="knop" href="{{ route('pakketten.index') }}">Bekijk onze pakketten</a>
        </div>
    </section>
@endsection

@section('content')
    @if ($producten->isNotEmpty())
        <h2 style="margin-top: 0;">Onze favorieten</h2>

        <div class="favorieten">
            @foreach ($producten as $product)
                <div class="kaart">
                    <div class="afbeelding"></div>
                    <h3>{{ $product->naam }}</h3>
                    <p class="muted">{{ $product->beschrijving }}</p>
                    <p class="prijs">Vanaf &euro; {{ number_format($product->prijs, 2, ',', '.') }}</p>
                </div>
            @endforeach
        </div>
    @endif

    <div class="banner">
        <x-icon name="calendar" :size="36" />
        <div class="banner-tekst">
            <strong>Ook voor speciale gelegenheden!</strong>
            <span>Neem contact met ons op voor de mogelijkheden.</span>
        </div>
        <a class="knop" href="{{ route('contact') }}">Contact opnemen</a>
    </div>
@endsection