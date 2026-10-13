@extends('layouts.app')

@section('title', 'Bestellen | Hap &amp; Hop')

@section('content')
    <x-pagina-kop :titel="'Bestel: ' . $pakket->naam">
        {{ str_replace(["\r\n", "\n"], ', ', $pakket->beschrijving) }}
        &middot; &euro; {{ number_format($pakket->prijs, 2, ',', '.') }}
    </x-pagina-kop>

    <form class="formulier" method="POST" action="{{ route('bestellen.store', $pakket) }}">
        @csrf

        <label for="naam">Naam *</label>
        <input type="text" id="naam" name="naam" value="{{ old('naam', auth()->user()?->name) }}" required>
        @error('naam') <div class="fout">{{ $message }}</div> @enderror

        <label for="email">E-mailadres *</label>
        <input type="email" id="email" name="email" value="{{ old('email', auth()->user()?->email) }}" required>
        @error('email') <div class="fout">{{ $message }}</div> @enderror

        <label for="telefoon">Telefoonnummer (optioneel)</label>
        <input type="text" id="telefoon" name="telefoon" value="{{ old('telefoon') }}">
        @error('telefoon') <div class="fout">{{ $message }}</div> @enderror

        <label for="aantal">Aantal pakketten *</label>
        <input type="number" id="aantal" name="aantal" min="1" max="100" value="{{ old('aantal', 1) }}" required>
        @error('aantal') <div class="fout">{{ $message }}</div> @enderror

        <label for="gewenste_datum">Gewenste datum *</label>
        <input type="date" id="gewenste_datum" name="gewenste_datum"
               min="{{ now()->addDay()->format('Y-m-d') }}"
               value="{{ old('gewenste_datum') }}" required>
        @error('gewenste_datum') <div class="fout">{{ $message }}</div> @enderror

        <label for="opmerking">Opmerking (optioneel, bijvoorbeeld allergieën)</label>
        <textarea id="opmerking" name="opmerking" rows="4">{{ old('opmerking') }}</textarea>
        @error('opmerking') <div class="fout">{{ $message }}</div> @enderror

        <button type="submit" class="knop">Bestelling plaatsen</button>
    </form>
@endsection