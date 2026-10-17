@extends('mail.layout')

@section('titel', $bestelling->wordtOpgehaald() ? 'Je bestelling staat klaar' : 'Je bestelling is onderweg')

@section('inhoud')
    <h1 style="margin: 0 0 12px; font-size: 22px;">Goed nieuws, {{ $bestelling->naam }}!</h1>

    <p style="margin: 0 0 16px;">
        @if ($bestelling->wordtOpgehaald())
            Je bestelling staat klaar om op te halen.
        @else
            Je bestelling is verzonden en komt eraan.
        @endif
    </p>

    <p style="margin: 0 0 16px;">
        <strong>Bestelnummer:</strong> #{{ $bestelling->id }}<br>
        <strong>{{ $bestelling->wordtOpgehaald() ? 'Ophalen op' : 'Gewenste datum' }}:</strong> {{ $bestelling->gewenste_datum->format('d-m-Y') }}
    </p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin: 0 0 16px; border-top: 1px solid #e5e7eb;">
        @foreach ($bestelling->regels as $regel)
            <tr>
                <td style="padding: 8px 0; border-bottom: 1px solid #e5e7eb;">{{ $regel->aantal }}&times; {{ $regel->pakket_naam }}</td>
            </tr>
        @endforeach
    </table>

    <p style="margin: 16px 0 0;">
        Vragen over je bestelling? Beantwoord dan gewoon deze e-mail.
    </p>
@endsection
