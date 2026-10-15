@extends('layouts.app')

@section('title', 'Wachtwoord vergeten | Hap &amp; Hop')

@section('content')
    <x-pagina-kop titel="Wachtwoord vergeten">
        Vul je e-mailadres in. Hoort er een account bij, dan sturen we je een link om een nieuw wachtwoord in te stellen.
    </x-pagina-kop>

    <form class="formulier" method="POST" action="{{ route('password.email') }}">
        @csrf

        <label for="email">E-mailadres</label>
        <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus>
        @error('email') <div class="fout">{{ $message }}</div> @enderror

        <button type="submit" class="knop">Link versturen</button>
    </form>

    <p style="margin-top: 16px;"><a href="{{ route('login') }}">Terug naar inloggen</a></p>
@endsection