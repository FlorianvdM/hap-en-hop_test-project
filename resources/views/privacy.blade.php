@extends('layouts.app')

@section('title', 'Privacyverklaring | Hap &amp; Hop')

@section('content')
    <div class="tekstpagina">
        <x-pagina-kop titel="Privacyverklaring">
            Hier lees je hoe Hap &amp; Hop omgaat met jouw gegevens. Laatst bijgewerkt: 14 oktober 2026.
        </x-pagina-kop>

        <h2>Wie wij zijn</h2>
        <p>
            Hap &amp; Hop is een kleine cateraar in Houten en omgeving. Voor vragen over privacy kun je mailen naar
            <a href="mailto:{{ config('hapenhop.email') }}">{{ config('hapenhop.email') }}</a>.
        </p>

        <h2>Welke gegevens wij verwerken en waarom</h2>
        <ul>
            <li>
                <strong>Bestellingen:</strong> naam, e-mailadres, telefoonnummer (optioneel), gewenste datum en je
                opmerking. Dat gebruiken we om je bestelling voor te bereiden en contact met je op te nemen.
            </li>
            <li>
                <strong>Contactformulier:</strong> naam, e-mailadres en je bericht. Dat gebruiken we om je vraag te
                beantwoorden.
            </li>
            <li>
                <strong>Account:</strong> naam, e-mailadres en wachtwoord. Je wachtwoord wordt versleuteld opgeslagen,
                zodat wij het zelf niet kunnen lezen. Met je account kun je inloggen en je bestellingen terugzien.
            </li>
        </ul>
        <p>
            Geef je allergieën of dieetwensen door, dan gebruiken we die alleen om je bestelling goed en veilig voor te
            bereiden.
        </p>

        <h2>Hoe lang wij gegevens bewaren</h2>
        <ul>
            <li>Bestelgegevens bewaren we zolang dat nodig is voor de uitvoering en onze administratie. Voor de administratie geldt een wettelijke bewaartermijn van zeven jaar.</li>
            <li>Berichten uit het contactformulier bewaren we niet langer dan nodig is om je vraag af te handelen.</li>
            <li>Een account bewaren we tot je vraagt om het te verwijderen.</li>
        </ul>

        <h2>Delen met anderen</h2>
        <p>
            Wij verkopen jouw gegevens nooit. We delen alleen gegevens met partijen die we nodig hebben voor onze
            dienstverlening, zoals onze hostingprovider, onze e-mailprovider en, bij online betalen, onze betaalprovider.
        </p>

        <h2>Cookies</h2>
        <p>
            Wij gebruiken alleen functionele cookies. Die zijn nodig om de website te laten werken, bijvoorbeeld om
            ingelogd te blijven en om formulieren te beveiligen. Wij gebruiken geen tracking- of advertentiecookies.
        </p>

        <h2>Beveiliging</h2>
        <p>
            Wij nemen passende maatregelen om jouw gegevens te beschermen. Wachtwoorden worden versleuteld opgeslagen
            en alleen beheerders hebben toegang tot bestellingen en berichten.
        </p>

        <h2>Jouw rechten</h2>
        <p>
            Je mag ons vragen om inzage in jouw gegevens, om ze te corrigeren of te verwijderen, om de verwerking te
            beperken, of om bezwaar te maken. Je kunt ook vragen om je gegevens over te dragen. Stuur daarvoor een mail
            naar <a href="mailto:{{ config('hapenhop.email') }}">{{ config('hapenhop.email') }}</a>. We reageren binnen
            een maand.
        </p>
        <p>
            Ben je niet tevreden over hoe wij met je gegevens omgaan? Dan kun je een klacht indienen bij de Autoriteit
            Persoonsgegevens (autoriteitpersoonsgegevens.nl).
        </p>
    </div>
@endsection