@extends('layouts.app')

@section('title', 'Home | Hap & Hop')

@section('hero')
    <section class="hero">
        <div class="wrap hero-inner">
            <div>
                <h1>Verse broodjes en heerlijke catering in Houten</h1>
                <p class="lead">
                    Van ontbijt tot lunch: pakketten en broodjes die we vers voor je klaarmaken.
                    Je bestelt online, wij bezorgen of je haalt ze zelf op.
                </p>

                <div class="hero-knoppen">
                    <a class="knop knop-goud" href="{{ route('pakketten.index') }}">Bekijk de pakketten</a>
                    <a class="knop knop-rand" href="{{ route('contact') }}">Catering op maat aanvragen</a>
                </div>

                <ul class="feiten">
                    <li><x-icon name="truck" /> Bezorgen of afhalen</li>
                    <li><x-icon name="credit-card" /> Pin, contant of op factuur</li>
                    <li><x-icon name="clock" /> Ma t/m vr 08.00 &ndash; 16.00</li>
                </ul>
            </div>

            <div class="hero-merk">
                <img src="{{ asset('images/logo-merk.png') }}" alt="Logo van Hap &amp; Hop">
            </div>
        </div>
    </section>
@endsection

@section('breed')
    {{-- Zo bestel je: een echte volgorde, daarom met nummers --}}
    <section class="sectie">
        <div class="wrap">
            <div class="sectie-kop">
                <div>
                    <h2>Zo bestel je</h2>
                    <p>In drie stappen staat je lunch of ontbijt klaar.</p>
                </div>
            </div>

            <div class="stappen">
                <div class="stap">
                    <div class="stap-nr">1</div>
                    <h3>Kies je pakket of broodjes</h3>
                    <p>Bekijk de pakketten en losse broodjes en leg ze in de winkelwagen.</p>
                </div>
                <div class="stap">
                    <div class="stap-nr">2</div>
                    <h3>Vul je gegevens in</h3>
                    <p>Kies een datum en of je zelf ophaalt of wij bezorgen. Geef je dieetwensen door.</p>
                </div>
                <div class="stap">
                    <div class="stap-nr">3</div>
                    <h3>Wij maken het vers klaar</h3>
                    <p>Je krijgt meteen een bevestiging per e-mail. Op de dag zelf staat alles klaar of bezorgen wij het.</p>
                </div>
            </div>
        </div>
    </section>

    @if ($pakketten->isNotEmpty())
        <section class="sectie sectie-wit">
            <div class="wrap">
                <div class="sectie-kop">
                    <div>
                        <h2>Onze pakketten</h2>
                        <p>Compleet samengesteld, vers bereid en met zorg ingepakt.</p>
                    </div>
                    <a class="link" href="{{ route('pakketten.index') }}">Alle pakketten bekijken</a>
                </div>

                <div class="kaarten">
                    @foreach ($pakketten as $pakket)
                        <x-pakket-kaart :pakket="$pakket" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($producten->isNotEmpty())
        <section class="sectie">
            <div class="wrap">
                <div class="sectie-kop">
                    <div>
                        <h2>Onze favorieten</h2>
                        <p>Los te bestellen, ook als aanvulling op een pakket.</p>
                    </div>
                    <a class="link" href="{{ route('producten') }}">Alle broodjes bekijken</a>
                </div>

                <div class="kaarten">
                    @foreach ($producten as $product)
                        <x-product-kaart :product="$product" :compact="true" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($reviews->isNotEmpty())
        <section class="sectie sectie-wit">
            <div class="wrap">
                <div class="sectie-kop">
                    <div>
                        <h2>Wat klanten zeggen</h2>
                    </div>
                </div>

                <div class="reviews">
                    @foreach ($reviews as $review)
                        <figure class="review">
                            <div class="sterren" aria-label="{{ $review->beoordeling }} van de 5 sterren">
                                {{ str_repeat('★', $review->beoordeling) }}{{ str_repeat('☆', 5 - $review->beoordeling) }}
                            </div>
                            <blockquote>{{ $review->tekst }}</blockquote>
                            <figcaption>{{ $review->naam }}</figcaption>
                        </figure>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <section class="sectie">
        <div class="wrap">
            <div class="cta">
                <div>
                    <h2>Ook voor speciale gelegenheden</h2>
                    <p>Neem contact met ons op voor de mogelijkheden.</p>
                </div>
                <a class="knop knop-goud" href="{{ route('contact') }}">Contact opnemen</a>
            </div>
        </div>
    </section>
@endsection
