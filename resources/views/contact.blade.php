@extends('layouts.app')

@section('title', 'Contact | Hap & Hop')

@section('kop')
    <x-pagina-kop titel="Contact">
        Heb je een vraag, wil je een bestelling plaatsen of sparren over maatwerk? Neem gerust contact met ons op.
    </x-pagina-kop>
@endsection

@section('content')
    @php
        // Contactgegevens uit config/hapenhop.php (en dus uit .env)
        $telefoon = config('hapenhop.telefoon');
        $email = config('hapenhop.email');

        // Voor de belknop op een telefoon: alleen cijfers en een eventuele plus
        $telefoonLink = $telefoon ? preg_replace('/[^+\d]/', '', $telefoon) : null;
    @endphp

    <div class="contact-raster">
        <form class="blok" method="POST" action="{{ route('contact.store') }}" style="margin: 0;">
            @csrf

            <div class="veld">
                <label for="naam">Naam *</label>
                <input type="text" id="naam" name="naam" placeholder="Je naam"
                    value="{{ old('naam', auth()->user()?->name) }}" required>
                @error('naam') <div class="fout">{{ $message }}</div> @enderror
            </div>

            <div class="veld">
                <label for="email">E-mailadres *</label>
                <input type="email" id="email" name="email" placeholder="Je e-mailadres"
                    value="{{ old('email', auth()->user()?->email) }}" required>
                @error('email') <div class="fout">{{ $message }}</div> @enderror
            </div>

            <div class="veld">
                <label for="bericht">Bericht *</label>
                <textarea id="bericht" name="bericht" rows="6" placeholder="Typ je bericht..." style="min-height: 180px;"
                    required>{{ old('bericht') }}</textarea>
                @error('bericht') <div class="fout">{{ $message }}</div> @enderror
            </div>

            <div style="margin-top: 22px;">
                <button type="submit" class="knop">Versturen</button>
            </div>
        </form>

        <aside class="contact-kaart">
            <h2>Zo bereik je ons</h2>

            {{-- De telefoonregel staat er alleen als er een nummer is ingesteld --}}
            @if ($telefoon)
                <div class="contact-item">
                    <x-icon name="phone" />
                    <div>
                        <strong>Telefoon</strong>
                        <a href="tel:{{ $telefoonLink }}">{{ $telefoon }}</a>
                    </div>
                </div>
            @endif

            <div class="contact-item">
                <x-icon name="mail" />
                <div>
                    <strong>E-mail</strong>
                    <a href="mailto:{{ $email }}">{{ $email }}</a>
                </div>
            </div>

            <div class="contact-item">
                <x-icon name="map-pin" />
                <div>
                    <strong>Adres</strong>
                    <span>{{ config('hapenhop.adres') }}</span>
                </div>
            </div>

            <div class="contact-item">
                <x-icon name="clock" />
                <div>
                    <strong>Broodjes</strong>
                    <span>Elke donderdag, bestellen voor 10.30 uur</span>
                </div>
            </div>
        </aside>
    </div>
@endsection
