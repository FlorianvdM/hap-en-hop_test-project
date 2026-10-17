@extends('mail.layout')

@section('titel', 'Nieuwe bestelling')

@section('inhoud')
    <h1 style="margin: 0 0 12px; font-size: 22px;">Nieuwe bestelling #{{ $bestelling->id }}</h1>

    <p style="margin: 0 0 16px;">
        <strong>Klant:</strong> {{ $bestelling->naam }}<br>
        <strong>E-mail:</strong> {{ $bestelling->email }}<br>
        @if ($bestelling->telefoon)
            <strong>Telefoon:</strong> {{ $bestelling->telefoon }}<br>
        @endif
        <strong>Gewenste datum:</strong> {{ $bestelling->gewenste_datum->format('d-m-Y') }}<br>
        <strong>Levering:</strong> {{ $bestelling->leveringTekst() }}
        @if ($bestelling->bezorgadres())
            <br><strong>Adres:</strong> {!! nl2br(e($bestelling->bezorgadres())) !!}
        @endif
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
        <p style="margin: 0 0 12px; padding: 10px 12px; background: #fee2e2; color: #7f1d1d; border-radius: 6px;">
            <strong>Dieetwensen:</strong> {{ implode(', ', array_map('ucfirst', $bestelling->dieetwensen)) }}
        </p>
    @endif

    @if ($bestelling->opmerking)
        <p style="margin: 0 0 12px;">
            <strong>Opmerking van de klant:</strong><br>
            {!! nl2br(e($bestelling->opmerking)) !!}
        </p>
    @endif

    <p style="margin: 20px 0 0;">
        <a href="{{ route('admin.bestellingen') }}"
           style="display: inline-block; background: #175432; color: #d9b45a; padding: 10px 18px; border-radius: 8px; text-decoration: none; font-weight: bold;">
            Bekijk in het beheer
        </a>
    </p>

    <p style="margin: 16px 0 0; color: #6b7280; font-size: 14px;">
        Je kunt deze e-mail beantwoorden: het antwoord gaat rechtstreeks naar de klant.
    </p>
@endsection