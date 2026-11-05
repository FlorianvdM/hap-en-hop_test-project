@extends('layouts.app')

@section('title', 'Veelgestelde vragen | Hap & Hop')
@section('beschrijving', 'Antwoorden op veelgestelde vragen over bestellen, bezorgen, betalen en dieetwensen bij Hap & Hop.')

@section('kop')
    <x-pagina-kop titel="Veelgestelde vragen">
        Staat je vraag er niet tussen? <a href="{{ route('contact') }}">Neem contact met ons op</a>.
    </x-pagina-kop>
@endsection

@section('content')
    {{-- <details> klapt zonder JavaScript open en dicht, en werkt met het toetsenbord --}}
    <div class="vragen">
        <details>
            <summary>Wanneer kan ik broodjes bestellen?</summary>
            <p>Broodjes zijn er elke donderdag. Bestel voor 10.30 uur, dan kun je ze vanaf 12.00 uur ophalen of laten bezorgen.
                Je kunt ook al een donderdag later kiezen.</p>
        </details>

        <details>
            <summary>Kan ik pakketten ook op andere dagen bestellen?</summary>
            <p>Ja. Een pakket bestel je minstens een dag van tevoren. Zit er ook een broodje in je winkelwagen, dan kies je een donderdag.</p>
        </details>

        <details>
            <summary>Doen jullie ook catering op andere dagen?</summary>
            <p>Ja, catering kan ook op andere dagen. Vraag het aan via de <a href="{{ route('catering') . '#aanvraag' }}">cateringpagina</a>,
                dan maken we een voorstel met de prijs.</p>
        </details>

        <details>
            <summary>Bezorgen jullie?</summary>
            <p>Ja, wij bezorgen in Houten en omgeving. Je kunt je bestelling ook zelf ophalen op {{ config('hapenhop.adres') }}.</p>
        </details>

        <details>
            <summary>Hoe kan ik betalen?</summary>
            <p>Je kunt betalen met pin, contant of op factuur (voor zakelijke klanten).</p>
        </details>

        <details>
            <summary>Hebben jullie iets voor dieetwensen, zoals halal of vegetarisch?</summary>
            <p>Ja. Bij het bestellen kun je dieetwensen aanvinken, zoals vegetarisch, veganistisch, halal, glutenvrij en lactosevrij.
                Wij houden er rekening mee bij het klaarmaken.</p>
        </details>

        <details>
            <summary>Ik heb een allergie. Waar zie ik wat erin zit?</summary>
            <p>Bij de broodjes en pakketten staat welke allergenen erin zitten. Geef je allergie ook door bij je bestelling.
                We werken in één keuken, dus twijfel je, neem dan vooraf contact met ons op.</p>
        </details>

        <details>
            <summary>Moet ik een account hebben om te bestellen?</summary>
            <p>Nee, je kunt ook zonder account bestellen. Met een account zie je je eerdere bestellingen terug bij "Mijn bestellingen".</p>
        </details>

        <details>
            <summary>Kan ik mijn bestelling annuleren of wijzigen?</summary>
            <p>Neem dan zo snel mogelijk contact met ons op, dan kijken we samen wat er nog kan. Lees ook onze
                <a href="{{ route('voorwaarden') }}">algemene voorwaarden</a>.</p>
        </details>

        <details>
            <summary>Ik heb geen bevestigingsmail gekregen. Wat nu?</summary>
            <p>Kijk eerst in je map met ongewenste mail. Staat hij daar ook niet, mail ons dan op
                <a href="mailto:{{ config('hapenhop.email') }}">{{ config('hapenhop.email') }}</a>.</p>
        </details>
    </div>
@endsection
