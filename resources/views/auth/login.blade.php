@extends('layouts.app')

@section('title', 'Inloggen | Hap &amp; Hop')

@section('content')
    <h1>Inloggen</h1>

    <form class="formulier" method="POST" action="{{ route('login') }}">
        @csrf

        <label for="email">E-mailadres</label>
        <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus>
        @error('email') <div class="fout">{{ $message }}</div> @enderror

        <label for="password">Wachtwoord</label>
        <input type="password" id="password" name="password" required>
        @error('password') <div class="fout">{{ $message }}</div> @enderror

        <label>
            <input type="checkbox" name="onthouden" value="1" style="width:auto;"> Onthoud mij
        </label>

        <button type="submit" class="knop">Inloggen</button>
    </form>

    <p>Nog geen account? <a href="{{ route('register') }}">Registreer je hier</a>.</p>
@endsection