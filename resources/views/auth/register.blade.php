@extends('layouts.app')

@section('title', 'Registreren | Hap &amp; Hop')

@section('content')
    <h1>Account aanmaken</h1>

    <form class="formulier" method="POST" action="{{ route('register') }}">
        @csrf

        <label for="name">Naam</label>
        <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus>
        @error('name') <div class="fout">{{ $message }}</div> @enderror

        <label for="email">E-mailadres</label>
        <input type="email" id="email" name="email" value="{{ old('email') }}" required>
        @error('email') <div class="fout">{{ $message }}</div> @enderror

        <label for="password">Wachtwoord (minimaal 8 tekens)</label>
        <input type="password" id="password" name="password" required>
        @error('password') <div class="fout">{{ $message }}</div> @enderror

        <label for="password_confirmation">Herhaal wachtwoord</label>
        <input type="password" id="password_confirmation" name="password_confirmation" required>

        <button type="submit" class="knop">Registreren</button>
    </form>

    <p>Heb je al een account? <a href="{{ route('login') }}">Log hier in</a>.</p>
@endsection