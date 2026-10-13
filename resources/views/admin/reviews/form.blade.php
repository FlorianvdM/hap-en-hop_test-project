@extends('layouts.admin')

@section('title', ($review->exists ? 'Review wijzigen' : 'Review toevoegen') . ' | Hap &amp; Hop')

@section('content')
    <h1>{{ $review->exists ? 'Review wijzigen' : 'Review toevoegen' }}</h1>

    <form class="formulier" method="POST"
          action="{{ $review->exists ? route('admin.reviews.update', $review) : route('admin.reviews.store') }}">
        @csrf
        @if ($review->exists)
            @method('PUT')
        @endif

        <label for="naam">Naam van de klant *</label>
        <input type="text" id="naam" name="naam" value="{{ old('naam', $review->naam) }}" required>
        @error('naam') <div class="fout">{{ $message }}</div> @enderror

        <label for="tekst">Review *</label>
        <textarea id="tekst" name="tekst" rows="5" required>{{ old('tekst', $review->tekst) }}</textarea>
        @error('tekst') <div class="fout">{{ $message }}</div> @enderror

        <label for="beoordeling">Beoordeling *</label>
        <select id="beoordeling" name="beoordeling">
            @foreach ([5, 4, 3, 2, 1] as $aantal)
                <option value="{{ $aantal }}" @selected((int) old('beoordeling', $review->beoordeling) === $aantal)>
                    {{ $aantal }} {{ $aantal === 1 ? 'ster' : 'sterren' }}
                </option>
            @endforeach
        </select>
        @error('beoordeling') <div class="fout">{{ $message }}</div> @enderror

        <label class="vinkje">
            <input type="hidden" name="actief" value="0">
            <input type="checkbox" name="actief" value="1" @checked((bool) old('actief', $review->actief))>
            Zichtbaar op de homepage
        </label>
        @error('actief') <div class="fout">{{ $message }}</div> @enderror

        <div class="acties" style="margin-top: 20px;">
            <button type="submit" class="knop">Opslaan</button>
            <a class="knop knop-licht" href="{{ route('admin.reviews.index') }}">Annuleren</a>
        </div>
    </form>
@endsection