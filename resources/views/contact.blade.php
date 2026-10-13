@extends('layouts.app')

@section('title', 'Contact | Hap &amp; Hop')

@section('content')
    <h1>Contact</h1>
    <p>Vragen of een speciale wens? Stuur ons een bericht.</p>

    <form class="formulier" method="POST" action="{{ route('contact.store') }}">
        @csrf

        <label for="naam">Naam</label>
        <input type="text" id="naam" name="naam" value="{{ old('naam', auth()->user()?->name) }}" required>
        @error('naam') <div class="fout">{{ $message }}</div> @enderror

        <label for="email">E-mailadres</label>
        <input type="email" id="email" name="email" value="{{ old('email', auth()->user()?->email) }}" required>
        @error('email') <div class="fout">{{ $message }}</div> @enderror

        <label for="bericht">Bericht</label>
        <textarea id="bericht" name="bericht" rows="6" required>{{ old('bericht') }}</textarea>
        @error('bericht') <div class="fout">{{ $message }}</div> @enderror

        <button type="submit" class="knop">Versturen</button>
    </form>
@endsection