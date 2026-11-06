@extends('layouts.app')

@section('title', 'Bestellen | Hap & Hop')

@section('kop')
    <x-pagina-kop titel="Bestellen">
        Vul je gegevens in en kies voor wanneer je de bestelling nodig hebt.
    </x-pagina-kop>
@endsection

@section('content')
    <div class="raster-zij">
        <form method="POST" action="{{ route('bestellen.store') }}" data-laden="Bezig met bestellen...">
            @csrf

            <section class="blok">
                <h2>Jouw gegevens</h2>

                <div class="duo">
                    <div class="veld">
                        <label for="naam">Naam *</label>
                        <input type="text" id="naam" name="naam" value="{{ old('naam', auth()->user()?->name) }}" required>
                        @error('naam') <div class="fout">{{ $message }}</div> @enderror
                    </div>

                    <div class="veld">
                        <label for="email">E-mailadres *</label>
                        <input type="email" id="email" name="email" value="{{ old('email', auth()->user()?->email) }}" required>
                        @error('email') <div class="fout">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="veld">
                    <label for="telefoon">Telefoonnummer (optioneel)</label>
                    <input type="text" id="telefoon" name="telefoon" value="{{ old('telefoon') }}">
                    @error('telefoon') <div class="fout">{{ $message }}</div> @enderror
                </div>
            </section>

            <section class="blok">
                <h2>Wanneer en hoe</h2>

                <div class="veld" style="margin-top: 0; max-width: 280px;">
                    <label for="gewenste_datum">Gewenste datum *</label>
                    {{-- Broodjes en pakketten zijn er alleen op donderdag, daarom een keuzelijst met donderdagen --}}
                    <select id="gewenste_datum" name="gewenste_datum" required>
                        @foreach ($besteldagen as $dag)
                            <option value="{{ $dag }}" @selected(old('gewenste_datum') === $dag)>
                                {{ \Carbon\Carbon::parse($dag)->locale('nl')->translatedFormat('l j F') }}
                            </option>
                        @endforeach
                    </select>
                    <small>Broodjes en pakketten zijn er alleen op donderdag. Losse broodjes bestel je tot 10.30 uur op de dag zelf, een pakket minstens een dag van tevoren.</small>
                    @error('gewenste_datum') <div class="fout">{{ $message }}</div> @enderror
                </div>

                <div class="veld">
                    <span class="label">Ophalen of bezorgen *</span>
                    <div class="keuze">
                        <label class="tegel">
                            <input type="radio" name="levering" value="ophalen" @checked(old('levering', 'ophalen') === 'ophalen')>
                            <div>
                                <strong>Ik haal de bestelling zelf op</strong>
                                <span>Je haalt hem op in Houten.</span>
                            </div>
                        </label>
                        <label class="tegel">
                            <input type="radio" name="levering" value="bezorgen" @checked(old('levering') === 'bezorgen')>
                            <div>
                                <strong>Bezorgen</strong>
                                <span>Wij bezorgen in Houten en omgeving.</span>
                            </div>
                        </label>
                    </div>
                    @error('levering') <div class="fout">{{ $message }}</div> @enderror
                </div>

                {{-- Dit blok klapt open als de klant bezorgen kiest (zie public/js/site.js) --}}
                <div class="adres" id="bezorgadres" @if (old('levering') !== 'bezorgen') hidden @endif>
                    <div class="duo">
                        <div class="veld">
                            <label for="straat">Straat en huisnummer *</label>
                            <input type="text" id="straat" name="straat" value="{{ old('straat') }}" autocomplete="street-address">
                            @error('straat') <div class="fout">{{ $message }}</div> @enderror
                        </div>

                        <div class="veld">
                            <label for="postcode">Postcode *</label>
                            <input type="text" id="postcode" name="postcode" value="{{ old('postcode') }}" autocomplete="postal-code">
                            @error('postcode') <div class="fout">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="veld">
                        <label for="plaats">Plaats *</label>
                        <input type="text" id="plaats" name="plaats" value="{{ old('plaats') }}" autocomplete="address-level2">
                        @error('plaats') <div class="fout">{{ $message }}</div> @enderror
                    </div>
                </div>
            </section>

            <section class="blok">
                <h2>Dieetwensen en opmerking</h2>

                <div class="veld" style="margin-top: 0;">
                    <span class="label">Dieetwensen (optioneel)</span>
                    <div class="chips">
                        @foreach ($dieetwensen as $sleutel => $label)
                            <label class="chip">
                                <input type="checkbox" name="dieetwensen[]" value="{{ $sleutel }}"
                                    @checked(in_array($sleutel, (array) old('dieetwensen', []), true))>
                                {{ $label }}
                            </label>
                        @endforeach
                    </div>
                    @error('dieetwensen') <div class="fout">{{ $message }}</div> @enderror
                    @error('dieetwensen.*') <div class="fout">{{ $message }}</div> @enderror
                </div>

                <div class="veld">
                    <label for="opmerking">Opmerking (optioneel)</label>
                    <textarea id="opmerking" name="opmerking" rows="4">{{ old('opmerking') }}</textarea>
                    <small>Heb je een ernstige allergie? Vermeld dat dan ook hier.</small>
                    @error('opmerking') <div class="fout">{{ $message }}</div> @enderror
                </div>
            </section>

            <p class="muted">Door te bestellen ga je akkoord met onze
                <a href="{{ route('voorwaarden') }}" target="_blank" rel="noopener">algemene voorwaarden</a>.</p>
            <button type="submit" class="knop" style="min-height: 54px; padding: 0 40px; font-size: 17px;">Bestelling plaatsen</button>
        </form>

        <aside class="samenvatting">
            <h2>Jouw bestelling</h2>

            <ul>
                @foreach ($regels as $regel)
                    <li>
                        <span>{{ $regel->aantal }}&times; {{ $regel->item->naam }}</span>
                        <span>&euro; {{ number_format($regel->totaal, 2, ',', '.') }}</span>
                    </li>
                @endforeach
            </ul>

            <div class="totaal">
                <span>Totaal</span>
                <span>&euro; {{ number_format($totaal, 2, ',', '.') }}</span>
            </div>

            <a class="link" href="{{ route('winkelwagen.index') }}">Winkelwagen aanpassen</a>

            @guest
                <p class="notitie">
                    Wil je je bestelling later terugzien onder "Mijn bestellingen"?
                    <a href="{{ route('login') }}">Log in</a> of
                    <a href="{{ route('register') }}">maak een account aan</a>
                    voordat je bestelt. Je winkelwagen blijft bewaard.
                </p>
            @endguest
        </aside>
    </div>
@endsection
