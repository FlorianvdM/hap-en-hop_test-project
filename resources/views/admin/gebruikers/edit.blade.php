@extends('layouts.admin')

@section('title', 'Gebruiker wijzigen | Hap &amp; Hop')

@section('content')
    <h1>Gebruiker wijzigen</h1>

    <form class="formulier" method="POST" action="{{ route('admin.gebruikers.update', $gebruiker) }}">
        @csrf
        @method('PUT')

        <label for="name">Naam *</label>
        <input type="text" id="name" name="name" value="{{ old('name', $gebruiker->name) }}" required>
        @error('name') <div class="fout">{{ $message }}</div> @enderror

        <label for="email">E-mailadres *</label>
        <input type="email" id="email" name="email" value="{{ old('email', $gebruiker->email) }}" required>
        @error('email') <div class="fout">{{ $message }}</div> @enderror

        @if ($gebruiker->is(auth()->user()))
            <p class="muted" style="margin-top: 16px;">Je kunt je eigen rol niet wijzigen.</p>
        @else
            <label for="is_admin">Rol</label>
            <select id="is_admin" name="is_admin">
                <option value="0" @selected(! old('is_admin', $gebruiker->is_admin))>Klant</option>
                <option value="1" @selected((bool) old('is_admin', $gebruiker->is_admin))>Beheerder</option>
            </select>
            @error('is_admin') <div class="fout">{{ $message }}</div> @enderror
        @endif

        <label for="password">Nieuw wachtwoord (laat leeg om het huidige te houden)</label>
        <input type="password" id="password" name="password" autocomplete="new-password">
        @error('password') <div class="fout">{{ $message }}</div> @enderror

        <label for="password_confirmation">Herhaal nieuw wachtwoord</label>
        <input type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password">

        <div class="acties" style="margin-top: 20px;">
            <button type="submit" class="knop">Opslaan</button>
            <a class="knop knop-licht" href="{{ route('admin.gebruikers.index') }}">Annuleren</a>
        </div>
    </form>
@endsection