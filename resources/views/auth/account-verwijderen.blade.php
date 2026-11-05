@extends('layouts.app')

@section('title', 'Account verwijderen | Hap & Hop')

@section('content')
    <div class="uitloggen">
        <x-icon name="user" :size="48" />
        <h1>Account verwijderen</h1>
        <p class="muted" style="max-width: 560px;">
            Je account en je inloggegevens worden definitief verwijderd. Je bestellingen bewaren we nog voor onze
            administratie (wettelijk verplicht), maar ze zijn dan niet meer aan een account gekoppeld.
            Dit kun je niet ongedaan maken.
        </p>

        <form method="POST" action="{{ route('account.verwijderen') }}" style="width: 100%; max-width: 360px; text-align: left;">
            @csrf
            @method('DELETE')

            <div class="veld">
                <label for="wachtwoord">Vul ter bevestiging je wachtwoord in</label>
                <input type="password" id="wachtwoord" name="wachtwoord" autocomplete="current-password" required>
                @error('wachtwoord') <div class="fout">{{ $message }}</div> @enderror
            </div>

            <div class="knoppen" style="margin-top: 18px;">
                <button type="submit" class="knop">Account verwijderen</button>
                <a class="knop knop-licht" href="{{ route('mijn-bestellingen') }}">Annuleren</a>
            </div>
        </form>
    </div>
@endsection
