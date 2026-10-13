@extends('layouts.app')

@section('title', 'Contact | Hap &amp; Hop')

@section('content')
    <x-pagina-kop titel="Contact">
        Heb je een vraag, wil je een bestelling plaatsen of sparren over maatwerk? Neem gerust contact met ons op.
    </x-pagina-kop>

    <div class="contact-raster">
        <form class="formulier" method="POST" action="{{ route('contact.store') }}">
            @csrf

            <label for="naam">Naam *</label>
            <input type="text" id="naam" name="naam" placeholder="Je naam"
                   value="{{ old('naam', auth()->user()?->name) }}" required>
            @error('naam') <div class="fout">{{ $message }}</div> @enderror

            <label for="email">E-mailadres *</label>
            <input type="email" id="email" name="email" placeholder="Je e-mailadres"
                   value="{{ old('email', auth()->user()?->email) }}" required>
            @error('email') <div class="fout">{{ $message }}</div> @enderror

            <label for="bericht">Bericht *</label>
            <textarea id="bericht" name="bericht" rows="6" placeholder="Typ je bericht..." required>{{ old('bericht') }}</textarea>
            @error('bericht') <div class="fout">{{ $message }}</div> @enderror

            <button type="submit" class="knop">Versturen</button>
        </form>

        <aside class="contact-kaart">
            <div class="contact-item">
                <x-icon name="phone" />
                <div>
                    <strong>Telefoon</strong>
                    <span>Nog invullen</span>
                </div>
            </div>

            <div class="contact-item">
                <x-icon name="mail" />
                <div>
                    <strong>E-mail</strong>
                    <span>Haphopcatering@outlook.com</span>
                </div>
            </div>

            <div class="contact-item">
                <x-icon name="map-pin" />
                <div>
                    <strong>Adres</strong>
                    <span>Houten en omgeving</span>
                </div>
            </div>

            <div class="kaart-placeholder">
                <x-icon name="map-pin" :size="40" />
            </div>
        </aside>
    </div>
@endsection