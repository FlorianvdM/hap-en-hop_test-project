@extends('layouts.app')

@section('title', 'Catering | Hap & Hop')
@section('beschrijving', 'Catering op maat in Houten en omgeving: lunch op het werk, een verjaardag of een feest. Ook op andere dagen dan donderdag.')

@section('kop')
    <x-pagina-kop titel="Catering">
        Een lunch op het werk, een verjaardag of een feest: wij verzorgen het eten, ook op andere dagen dan donderdag.
    </x-pagina-kop>
@endsection

@section('content')
    {{-- Bewust zonder prijzen: die spreken we per aanvraag af --}}
    {{-- Formulier links, de vier blokjes rechts; op de telefoon onder elkaar --}}
    <div class="contact-raster">
        <section id="aanvraag">
            <h2>Catering aanvragen</h2>
            <p class="muted">Vul in wat je zoekt. We nemen contact met je op met een voorstel en de prijs.</p>

            <form class="blok" method="POST" action="{{ route('catering.store') }}">
                @csrf

                <div class="duo">
                    <div class="veld">
                        <label for="naam">Naam *</label>
                        <input type="text" id="naam" name="naam" value="{{ old('naam', auth()->user()?->name) }}" required maxlength="100">
                        @error('naam') <div class="fout">{{ $message }}</div> @enderror
                    </div>
                    <div class="veld">
                        <label for="email">E-mailadres *</label>
                        <input type="email" id="email" name="email" value="{{ old('email', auth()->user()?->email) }}" required maxlength="150">
                        @error('email') <div class="fout">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="duo" style="margin-top: 20px;">
                    <div class="veld">
                        <label for="telefoon">Telefoonnummer (optioneel)</label>
                        <input type="tel" id="telefoon" name="telefoon" value="{{ old('telefoon') }}" maxlength="30">
                        @error('telefoon') <div class="fout">{{ $message }}</div> @enderror
                    </div>
                    <div class="veld">
                        <label for="datum">Datum *</label>
                        <input type="date" id="datum" name="datum" min="{{ now()->addDay()->format('Y-m-d') }}" value="{{ old('datum') }}" required>
                        @error('datum') <div class="fout">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="duo" style="margin-top: 20px;">
                    <div class="veld">
                        <label for="personen">Aantal personen *</label>
                        <input type="number" id="personen" name="personen" min="1" max="1000" value="{{ old('personen') }}" required>
                        @error('personen') <div class="fout">{{ $message }}</div> @enderror
                    </div>
                    <div class="veld">
                        <label for="gelegenheid">Gelegenheid *</label>
                        <select id="gelegenheid" name="gelegenheid" required>
                            @foreach (\App\Http\Controllers\ContactController::GELEGENHEDEN as $sleutel => $label)
                                <option value="{{ $sleutel }}" @selected(old('gelegenheid') === $sleutel)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('gelegenheid') <div class="fout">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="veld">
                    <label for="wensen">Wensen (optioneel)</label>
                    <textarea id="wensen" name="wensen" rows="3" maxlength="1500"
                        placeholder="Bijvoorbeeld: broodjes en soep, twee personen vegetarisch, bezorgen in Houten">{{ old('wensen') }}</textarea>
                    @error('wensen') <div class="fout">{{ $message }}</div> @enderror
                </div>

                <x-honingpot />

                <div style="margin-top: 22px;">
                    <button type="submit" class="knop">Aanvraag versturen</button>
                </div>
            </form>
        </section>

        <aside class="info-raster" style="grid-template-columns: 1fr;">
            <x-info-blok icon="utensils" titel="Op maat">
                Vertel ons voor hoeveel personen en wat je wensen zijn. Wij stellen er een passend aanbod bij samen.
            </x-info-blok>

            <x-info-blok icon="clock" titel="Elke dag mogelijk">
                Onze broodjes zijn er op donderdag, maar catering kan ook op andere dagen. Vraag het op tijd aan.
            </x-info-blok>

            <x-info-blok icon="leaf" titel="Vers bereid">
                Alles wordt met veel aandacht bereid. Ook met dieetwensen, zoals vegetarisch of halal, houden we rekening.
            </x-info-blok>

            <x-info-blok icon="truck" titel="Ophalen of bezorgen">
                Ophalen kan op {{ config('hapenhop.adres') }}. Wij bezorgen ook in Houten en omgeving.
            </x-info-blok>
        </aside>
    </div>
@endsection
