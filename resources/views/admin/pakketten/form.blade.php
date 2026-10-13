@extends('layouts.admin')

@section('title', ($pakket->exists ? 'Pakket wijzigen' : 'Pakket toevoegen') . ' | Hap &amp; Hop')

@section('content')
    <h1>{{ $pakket->exists ? 'Pakket wijzigen' : 'Pakket toevoegen' }}</h1>

    <form class="formulier" method="POST"
          action="{{ $pakket->exists ? route('admin.pakketten.update', $pakket) : route('admin.pakketten.store') }}">
        @csrf
        @if ($pakket->exists)
            @method('PUT')
        @endif

        <label for="naam">Naam *</label>
        <input type="text" id="naam" name="naam" value="{{ old('naam', $pakket->naam) }}" required>
        @error('naam') <div class="fout">{{ $message }}</div> @enderror

        <label for="beschrijving">Inhoud (één onderdeel per regel)</label>
        <textarea id="beschrijving" name="beschrijving" rows="6">{{ old('beschrijving', $pakket->beschrijving) }}</textarea>
        @error('beschrijving') <div class="fout">{{ $message }}</div> @enderror

        <label for="prijs">Prijs (&euro;) *</label>
        <input type="number" id="prijs" name="prijs" step="0.01" min="0" value="{{ old('prijs', $pakket->prijs) }}" required>
        @error('prijs') <div class="fout">{{ $message }}</div> @enderror

        <label class="vinkje">
            <input type="hidden" name="actief" value="0">
            <input type="checkbox" name="actief" value="1" @checked((bool) old('actief', $pakket->actief))>
            Zichtbaar op de website
        </label>
        @error('actief') <div class="fout">{{ $message }}</div> @enderror

        <div class="acties" style="margin-top: 20px;">
            <button type="submit" class="knop">Opslaan</button>
            <a class="knop knop-licht" href="{{ route('admin.pakketten.index') }}">Annuleren</a>
        </div>
    </form>
@endsection