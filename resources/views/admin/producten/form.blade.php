@extends('layouts.admin')

@section('title', ($product->exists ? 'Product wijzigen' : 'Product toevoegen') . ' | Hap &amp; Hop')

@section('content')
    <h1>{{ $product->exists ? 'Product wijzigen' : 'Product toevoegen' }}</h1>

    <form class="formulier" method="POST"
          action="{{ $product->exists ? route('admin.producten.update', $product) : route('admin.producten.store') }}">
        @csrf
        @if ($product->exists)
            @method('PUT')
        @endif

        <label for="naam">Naam *</label>
        <input type="text" id="naam" name="naam" value="{{ old('naam', $product->naam) }}" required>
        @error('naam') <div class="fout">{{ $message }}</div> @enderror

        <label for="beschrijving">Korte beschrijving</label>
        <input type="text" id="beschrijving" name="beschrijving" value="{{ old('beschrijving', $product->beschrijving) }}">
        @error('beschrijving') <div class="fout">{{ $message }}</div> @enderror

        <label for="prijs">Vanaf-prijs (&euro;) *</label>
        <input type="number" id="prijs" name="prijs" step="0.01" min="0" value="{{ old('prijs', $product->prijs) }}" required>
        @error('prijs') <div class="fout">{{ $message }}</div> @enderror

        <label for="voorraad">Voorraad (aantal beschikbaar)</label>
        <input type="number" id="voorraad" name="voorraad" min="0" max="99999" step="1"
               value="{{ old('voorraad', $product->voorraad) }}" placeholder="Leeg = onbeperkt">
        @error('voorraad') <div class="fout">{{ $message }}</div> @enderror

        <label class="vinkje">
            <input type="hidden" name="actief" value="0">
            <input type="checkbox" name="actief" value="1" @checked((bool) old('actief', $product->actief))>
            Zichtbaar op de homepage
        </label>
        @error('actief') <div class="fout">{{ $message }}</div> @enderror

        <div class="acties" style="margin-top: 20px;">
            <button type="submit" class="knop">Opslaan</button>
            <a class="knop knop-licht" href="{{ route('admin.producten.index') }}">Annuleren</a>
        </div>
    </form>
@endsection