@extends('layouts.app')

@section('title', 'Home | Hap &amp; Hop')

@section('hero')
    <section class="hero">
        <div class="hero-inhoud">
            <span class="eyebrow">Houten en omgeving</span>
            <h1>Verse broodjes &amp; heerlijke <span class="accent">catering</span></h1>
            <p class="hero-sub">Voor elke gelegenheid, van ontbijt tot lunch.</p>

            <ul class="tags">
                <li><a class="tag" href="{{ route('pakketten.index') }}">Ontbijt</a></li>
                <li><a class="tag" href="{{ route('pakketten.index') }}">Lunch</a></li>
                <li><a class="tag" href="{{ route('contact') }}">Catering op maat</a></li>
            </ul>

            <div class="hero-knoppen">
                <a class="knop" href="{{ route('pakketten.index') }}">Bestel direct</a>
                <a class="knop knop-rand" href="{{ route('info') }}">Meer info</a>
            </div>

            <ul class="vinkjes">
                <li>Verse en lokale ingrediënten</li>
                <li>Bezorgen of afhalen</li>
                <li>Pin, contant of op factuur</li>
            </ul>
        </div>
    </section>
@endsection

@section('content')
    @if ($producten->isNotEmpty())
        <div class="sectie-kop">
            <span class="eyebrow">Wat heb je nodig?</span>
            <h2>Onze favorieten</h2>
        </div>

        <div class="favorieten">
            @foreach ($producten as $product)
                <div class="kaart">
                    <div class="afbeelding"></div>
                    <h3>{{ $product->naam }}</h3>
                    <p class="muted">{{ $product->beschrijving }}</p>
                    <p class="prijs">Vanaf &euro; {{ number_format($product->prijs, 2, ',', '.') }}</p>
                    @if ($product->voorraadTekst())
                        <p class="voorraad {{ $product->uitverkocht() ? 'voorraad-op' : '' }}">{{ $product->voorraadTekst() }}</p>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

    @if ($reviews->isNotEmpty())
        <div class="sectie-kop" style="margin-top: 48px;">
            <span class="eyebrow">Reviews</span>
            <h2>Wat klanten zeggen</h2>
        </div>

        <div class="reviews">
            @foreach ($reviews as $review)
                <figure class="review">
                    <div class="sterren" aria-label="{{ $review->beoordeling }} van de 5 sterren">
                        {{ str_repeat('★', $review->beoordeling) }}{{ str_repeat('☆', 5 - $review->beoordeling) }}
                    </div>
                    <blockquote>{{ $review->tekst }}</blockquote>
                    <figcaption class="naam">{{ $review->naam }}</figcaption>
                </figure>
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