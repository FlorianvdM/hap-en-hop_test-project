@extends('layouts.app')

@section('title', 'Info | Hap &amp; Hop')

@section('content')
    <h1>Over Hap &amp; Hop</h1>

    <p>
        Hap &amp; Hop is een kleine cateraar uit Houten. Wij verzorgen broodjes,
        borrelplanken en pakketten voor kleine en grote gelegenheden.
    </p>

    <h2>Wat wij doen</h2>
    <p>
        Kies een pakket, geef door voor wanneer je het nodig hebt en wij zorgen
        dat het klaarstaat. Heb je een speciale wens? Neem dan gerust contact met ons op.
    </p>

    <h2>Waar wij werken</h2>
    <p>Houten en omgeving.</p>

    <h2>Contact</h2>
    <p>
        Mail ons op
        <a href="mailto:Haphopcatering@outlook.com">Haphopcatering@outlook.com</a>
        of gebruik het <a href="{{ route('contact') }}">contactformulier</a>.
    </p>
@endsection