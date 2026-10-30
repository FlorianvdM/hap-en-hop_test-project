@extends('layouts.app')

@section('title', 'Inloggen | Hap & Hop')

@section('kop')
    <x-pagina-kop titel="Inloggen">
        Log in om je bestellingen terug te zien.
    </x-pagina-kop>
@endsection

@section('content')
    <div class="inlog">
    <form class="formulier" method="POST" action="{{ route('login') }}">
        @csrf

        <label for="email">E-mailadres</label>
        <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus>
        @error('email') <div class="fout">{{ $message }}</div> @enderror

        <label for="password">Wachtwoord</label>
        <input type="password" id="password" name="password" required>
        @error('password') <div class="fout">{{ $message }}</div> @enderror

        <label class="vinkje">
            <input type="checkbox" name="onthouden" value="1"> Onthoud mij
        </label>

        <button type="submit" class="knop">Inloggen</button>
    </form>

    <p><a href="{{ route('password.request') }}">Wachtwoord vergeten?</a></p>

    <p class="muted">Nog geen account? <a href="{{ route('register') }}">Registreer je hier</a>.</p>
    </div>
@endsection