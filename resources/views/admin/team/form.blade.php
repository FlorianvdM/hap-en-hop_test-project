@extends('layouts.admin')

@section('title', ($teamlid->exists ? 'Opdrachtgever wijzigen' : 'Opdrachtgever toevoegen') . ' | Hap &amp; Hop')

@section('content')
    <h1>{{ $teamlid->exists ? 'Opdrachtgever wijzigen' : 'Opdrachtgever toevoegen' }}</h1>

    <form class="formulier" method="POST" enctype="multipart/form-data"
          action="{{ $teamlid->exists ? route('admin.team.update', $teamlid) : route('admin.team.store') }}">
        @csrf
        @if ($teamlid->exists)
            @method('PUT')
        @endif

        <label for="naam">Naam *</label>
        <input type="text" id="naam" name="naam" value="{{ old('naam', $teamlid->naam) }}" required>
        @error('naam') <div class="fout">{{ $message }}</div> @enderror

        <label for="functie">Functie (bijvoorbeeld "Oprichter")</label>
        <input type="text" id="functie" name="functie" value="{{ old('functie', $teamlid->functie) }}">
        @error('functie') <div class="fout">{{ $message }}</div> @enderror

        <label for="biografie">Biografie</label>
        <textarea id="biografie" name="biografie" rows="7" maxlength="2000">{{ old('biografie', $teamlid->biografie) }}</textarea>
        @error('biografie') <div class="fout">{{ $message }}</div> @enderror

        <label for="foto">Foto (jpg, png of webp, maximaal 4 MB)</label>
        @if ($teamlid->fotoUrl())
            <img src="{{ $teamlid->fotoUrl() }}" alt="Huidige foto van {{ $teamlid->naam }}" width="80" height="80"
                 style="border-radius: 50%; object-fit: cover; display: block; margin-bottom: 8px;">
            <p class="muted">Kies een nieuw bestand om de foto te vervangen.</p>
        @endif
        <input type="file" id="foto" name="foto" accept="image/jpeg,image/png,image/webp">
        @error('foto') <div class="fout">{{ $message }}</div> @enderror

        <label for="volgorde">Volgorde (laagste nummer staat vooraan)</label>
        <input type="number" id="volgorde" name="volgorde" min="0" max="999"
               value="{{ old('volgorde', $teamlid->volgorde) }}">
        @error('volgorde') <div class="fout">{{ $message }}</div> @enderror

        <label class="vinkje">
            <input type="hidden" name="actief" value="0">
            <input type="checkbox" name="actief" value="1" @checked((bool) old('actief', $teamlid->actief))>
            Zichtbaar op de website
        </label>
        @error('actief') <div class="fout">{{ $message }}</div> @enderror

        <div class="acties" style="margin-top: 20px;">
            <button type="submit" class="knop">Opslaan</button>
            <a class="knop knop-licht" href="{{ route('admin.team.index') }}">Annuleren</a>
        </div>
    </form>
@endsection
