@extends('mail.layout')

@section('titel', 'We hebben je bestelling ontvangen')

@section('inhoud')
    <h1 style="margin: 0 0 12px; font-size: 22px;">Bedankt voor je bestelling, {{ $bestelling->naam }}!</h1>

    <p style="margin: 0 0 16px;">
        We hebben je bestelling ontvangen. We nemen zo snel mogelijk contact met je op om de details af te stemmen.
    </p>

    <p style="margin: 0 0 16px;">
        <strong>Bestelnummer:</strong> #{{ $bestelling->id }}<br>
        <strong>Gewenste datum:</strong> {{ $bestelling->gewenste_datum->format('d-m-Y') }}
    </p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin: 0 0 16px; border-top: 1px solid #e5e7eb;">
        @foreach ($bestelling->regels as $regel)
            <tr>
                <td style="padding: 8px 0; border-bottom: 1px solid #e5e7eb;">{{ $regel->aantal }}&times; {{ $regel->pakket_naam }}</td>
                <td align="right" style="padding: 8px 0; border-bottom: 1px solid #e5e7eb;">
                    &euro; {{ number_format($regel->aantal * $regel->prijs, 2, ',', '.') }}
                </td>
            </tr>
        @endforeach
        <tr>
            <td style="padding: 10px 0; font-weight: bold;">Totaal</td>
            <td align="right" style="padding: 10px 0; font-weight: bold;">&euro; {{ number_format($bestelling->totaal, 2, ',', '.') }}</td>
        </tr>
    </table>

    @if (! empty($bestelling->dieetwensen))
        <p style="margin: 0 0 12px;">
            <strong>Dieetwensen:</strong> {{ implode(', ', array_map('ucfirst', $bestelling->dieetwensen)) }}
        </p>
    @endif

    @if ($bestelling->opmerking)
        <p style="margin: 0 0 12px;">
            <strong>Je opmerking:</strong><br>
            {!! nl2br(e($bestelling->opmerking)) !!}
        </p>
    @endif

    <p style="margin: 16px 0 0;">
        Klopt er iets niet of wil je iets wijzigen? Beantwoord dan gewoon deze e-mail.
    </p>
@endsection