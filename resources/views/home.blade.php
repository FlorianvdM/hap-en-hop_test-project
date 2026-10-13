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
    @php
        $favorieten = [
            ['titel' => 'Luxe broodjes', 'tekst' => 'Diverse soorten, vers belegd', 'prijs' => '3,50'],
            ['titel' => 'Lunchpakketten', 'tekst' => 'Compleet en smakelijk', 'prijs' => '8,50'],
            ['titel' => 'Catering op maat', 'tekst' => 'Voor bedrijven en evenementen', 'prijs' => '12,50'],
        ];
    @endphp

    <h2 style="margin-top: 0;">Onze favorieten</h2>

    <div class="favorieten">
        @foreach ($favorieten as $favoriet)
            <div class="kaart">
                <div class="afbeelding"></div>
                <h3>{{ $favoriet['titel'] }}</h3>
                <p class="muted">{{ $favoriet['tekst'] }}</p>
                <p class="prijs">Vanaf &euro; {{ $favoriet['prijs'] }}</p>
            </div>
        @endforeach
    </div>

    <div class="banner">
        <x-icon name="calendar" :size="36" />
        <div class="banner-tekst">
            <strong>Ook voor speciale gelegenheden!</strong>
            <span>Neem contact met ons op voor de mogelijkheden.</span>
        </div>
        <a class="knop" href="{{ route('contact') }}">Contact opnemen</a>
    </div>
@endsection