@extends('mail.layout')

@section('titel', 'Nieuw wachtwoord instellen')

@section('inhoud')
    <h1 style="margin: 0 0 12px; font-size: 22px;">Nieuw wachtwoord instellen</h1>

    <p style="margin: 0 0 16px;">
        Je hebt gevraagd om je wachtwoord voor Hap &amp; Hop te wijzigen. Klik op de knop om een nieuw wachtwoord in te stellen.
        De link is {{ config('auth.passwords.users.expire') }} minuten geldig.
    </p>

    <p style="margin: 0 0 20px;">
        <a href="{{ $url }}"
           style="display: inline-block; background: #175432; color: #d9b45a; padding: 12px 20px; border-radius: 8px; text-decoration: none; font-weight: bold;">
            Nieuw wachtwoord instellen
        </a>
    </p>

    <p style="margin: 0 0 12px; color: #6b7280; font-size: 14px;">
        Werkt de knop niet? Kopieer dan deze link in je browser:<br>
        <span style="word-break: break-all;">{{ $url }}</span>
    </p>

    <p style="margin: 0; color: #6b7280; font-size: 14px;">
        Heb jij dit niet aangevraagd? Dan hoef je niets te doen. Je wachtwoord blijft hetzelfde.
    </p>
@endsection