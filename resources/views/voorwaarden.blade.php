@extends('layouts.app')

@section('title', 'Algemene voorwaarden | Hap & Hop')
@section('beschrijving', 'De algemene voorwaarden voor bestellingen bij Hap & Hop.')

@section('kop')
    <x-pagina-kop titel="Algemene voorwaarden">
        Deze voorwaarden gelden voor elke bestelling bij Hap &amp; Hop. Laatst bijgewerkt: 5 november 2026.
    </x-pagina-kop>
@endsection

{{-- Concept: Hap & Hop moet deze tekst zelf nalezen en goedkeuren voordat de site live gaat --}}
@section('content')
    <div class="tekstpagina">

        <h2>Wie wij zijn</h2>
        <p>
            Hap &amp; Hop, {{ config('hapenhop.adres') }}.
            E-mail: <a href="mailto:{{ config('hapenhop.email') }}">{{ config('hapenhop.email') }}</a>@if (config('hapenhop.telefoon')),
            telefoon: {{ config('hapenhop.telefoon') }}@endif.
            @if (config('hapenhop.kvk'))
                KvK-nummer: {{ config('hapenhop.kvk') }}.
            @endif
        </p>

        <h2>Bestellen</h2>
        <ul>
            <li>Je bestelling is definitief zodra je de bevestiging per e-mail hebt ontvangen.</li>
            <li>Broodjes zijn er alleen op donderdag. Bestel ze voor 10.30 uur, dan kun je ze vanaf 12.00 uur ophalen of laten bezorgen.</li>
            <li>Pakketten zijn er ook alleen op donderdag. Bestel ze minstens een dag van tevoren.</li>
            <li>Catering spreken we per aanvraag met je af, ook de prijs.</li>
        </ul>

        <h2>Prijzen en betalen</h2>
        <p>
            Je betaalt de prijs die op de website stond op het moment van bestellen. Betalen kan met pin, contant of
            op factuur (voor zakelijke klanten).
        </p>

        <h2>Annuleren en wijzigen</h2>
        <p>
            Wil je je bestelling annuleren of iets wijzigen? Neem dan zo snel mogelijk contact met ons op, dan kijken
            we samen wat er nog kan.
        </p>
        <p>
            Omdat wij vers eten bereiden dat snel bederft, geldt de wettelijke bedenktijd van 14 dagen niet voor
            onze bestellingen.
        </p>

        <h2>Ophalen en bezorgen</h2>
        <p>
            Ophalen doe je op {{ config('hapenhop.adres') }}. Wij bezorgen in Houten en omgeving op het adres dat je
            bij de bestelling opgeeft. Bezorgen kost &euro; {{ number_format(config('hapenhop.bezorgkosten'), 2, ',', '.') }} per bestelling. Zorg dat er op dat moment iemand is om de bestelling aan te nemen.
        </p>

        <h2>Allergenen en dieetwensen</h2>
        <p>
            Bij de broodjes en pakketten staat welke allergenen erin zitten. Heb je een allergie of dieetwens, geef dat
            dan door bij je bestelling. We werken in één keuken, dus we kunnen niet garanderen dat er nergens sporen
            van allergenen in zitten. Twijfel je, neem dan vooraf contact met ons op.
        </p>

        <h2>Klachten</h2>
        <p>
            Is er iets niet goed? Laat het ons dan dezelfde dag weten via
            <a href="mailto:{{ config('hapenhop.email') }}">{{ config('hapenhop.email') }}</a>, dan zoeken we samen
            een oplossing.
        </p>

        <h2>Privacy</h2>
        <p>
            Hoe wij met je gegevens omgaan, lees je in de <a href="{{ route('privacy') }}">privacyverklaring</a>.
        </p>

        <h2>Toepasselijk recht</h2>
        <p>Op deze voorwaarden en op elke bestelling is Nederlands recht van toepassing.</p>
    </div>
@endsection
