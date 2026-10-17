@extends('layouts.app')

@section('title', 'Bestellen | Hap &amp; Hop')

@section('content')
    <x-pagina-kop titel="Bestellen">
        Vul je gegevens in en kies voor wanneer je de bestelling nodig hebt.
    </x-pagina-kop>

    <div class="bestel-raster">
        <form class="formulier" method="POST" action="{{ route('bestellen.store') }}" style="margin-top: -16px;" data-laden="Bezig met bestellen...">
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

            <label for="gewenste_datum">Gewenste datum *</label>
            <input type="date" id="gewenste_datum" name="gewenste_datum"
                   min="{{ now()->addDay()->format('Y-m-d') }}"
                   value="{{ old('gewenste_datum') }}" required>
            @error('gewenste_datum') <div class="fout">{{ $message }}</div> @enderror

            <fieldset>
                <legend>Ophalen of bezorgen *</legend>

                <label class="vinkje">
                    <input type="radio" name="levering" value="ophalen" @checked(old('levering', 'ophalen') === 'ophalen')>
                    Ik haal de bestelling zelf op
                </label>
                <label class="vinkje">
                    <input type="radio" name="levering" value="bezorgen" @checked(old('levering') === 'bezorgen')>
                    Bezorgen
                </label>
            </fieldset>
            @error('levering') <div class="fout">{{ $message }}</div> @enderror

            <div id="bezorgadres" @if (old('levering') !== 'bezorgen') hidden @endif>
                <label for="straat">Straat en huisnummer *</label>
                <input type="text" id="straat" name="straat" value="{{ old('straat') }}" autocomplete="street-address">
                @error('straat') <div class="fout">{{ $message }}</div> @enderror

                <label for="postcode">Postcode *</label>
                <input type="text" id="postcode" name="postcode" value="{{ old('postcode') }}" autocomplete="postal-code">
                @error('postcode') <div class="fout">{{ $message }}</div> @enderror

                <label for="plaats">Plaats *</label>
                <input type="text" id="plaats" name="plaats" value="{{ old('plaats') }}" autocomplete="address-level2">
                @error('plaats') <div class="fout">{{ $message }}</div> @enderror
            </div>

            <fieldset>
                <legend>Dieetwensen (optioneel)</legend>

                @foreach ($dieetwensen as $sleutel => $label)
                    <label class="vinkje">
                        <input type="checkbox" name="dieetwensen[]" value="{{ $sleutel }}"
                               @checked(in_array($sleutel, (array) old('dieetwensen', []), true))>
                        {{ $label }}
                    </label>
                @endforeach
            </fieldset>
            @error('dieetwensen') <div class="fout">{{ $message }}</div> @enderror
            @error('dieetwensen.*') <div class="fout">{{ $message }}</div> @enderror

            <label for="opmerking">Opmerking (optioneel)</label>
            <textarea id="opmerking" name="opmerking" rows="4">{{ old('opmerking') }}</textarea>
            <p class="muted" style="margin: 4px 0 0; font-size: 0.9rem;">
                Heb je een ernstige allergie? Vermeld dat dan ook hier.
            </p>
            @error('opmerking') <div class="fout">{{ $message }}</div> @enderror

            <button type="submit" class="knop">Bestelling plaatsen</button>
        </form>

        <aside class="samenvatting">
            <h2>Jouw bestelling</h2>

            <ul>
                @foreach ($regels as $regel)
                    <li>
                        <span>{{ $regel->aantal }}&times; {{ $regel->pakket->naam }}</span>
                        <span>&euro; {{ number_format($regel->totaal, 2, ',', '.') }}</span>
                    </li>
                @endforeach
            </ul>

            <div class="totaal">
                <span>Totaal</span>
                <span>&euro; {{ number_format($totaal, 2, ',', '.') }}</span>
            </div>

            <p style="margin: 16px 0 0;">
                <a href="{{ route('winkelwagen.index') }}">Winkelwagen aanpassen</a>
            </p>

            @guest
                <p class="muted" style="margin: 16px 0 0; font-size: 0.9rem;">
                    Wil je je bestelling later terugzien onder "Mijn bestellingen"?
                    <a href="{{ route('login') }}">Log in</a> of
                    <a href="{{ route('register') }}">maak een account aan</a>
                    voordat je bestelt. Je winkelwagen blijft bewaard.
                </p>
            @endguest
        </aside>
    </div>
@endsection