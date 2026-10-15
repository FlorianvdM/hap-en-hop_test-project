@extends('layouts.app')

@section('title', 'Nieuw wachtwoord | Hap &amp; Hop')

@section('content')
    <x-pagina-kop titel="Nieuw wachtwoord instellen">
        Kies een nieuw wachtwoord van minimaal 8 tekens.
    </x-pagina-kop>

    <form class="formulier" method="POST" action="{{ route('password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <label for="email">E-mailadres</label>
        <input type="email" id="email" name="email" value="{{ old('email', $email) }}" required>
        @error('email') <div class="fout">{{ $message }}</div> @enderror

        <label for="password">Nieuw wachtwoord</label>
        <input type="password" id="password" name="password" required autofocus autocomplete="new-password">
        @error('password') <div class="fout">{{ $message }}</div> @enderror

        <label for="password_confirmation">Herhaal nieuw wachtwoord</label>
        <input type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password">

        <button type="submit" class="knop">Wachtwoord opslaan</button>
    </form>
@endsection