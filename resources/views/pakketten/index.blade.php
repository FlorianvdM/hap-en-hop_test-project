@extends('layouts.app')

@section('title', 'Pakketten | Hap & Hop')

@section('kop')
    <x-pagina-kop titel="Onze pakketten">
        Kies het pakket dat bij jou past. Al onze pakketten zijn vers bereid en met zorg samengesteld.
    </x-pagina-kop>
@endsection

@section('content')
    <section class="advies">
        <div>
            <h2>Weet je niet welk pakket past?</h2>
            <p>
                Vertel hoeveel personen je verwacht en waar je op let. Je krijgt een advies van onze
                <strong>AI-assistent</strong>. Vul geen persoonsgegevens in.
            </p>
        </div>

        <div>
            <form class="advies-form" method="POST" action="{{ route('pakketten.advies') }}">
                @csrf

                <div class="veld">
                    <label for="aantal">Aantal personen</label>
                    <input type="number" id="aantal" name="aantal" min="1" max="500" value="{{ old('aantal') }}" required>
                    @error('aantal') <div class="fout">{{ $message }}</div> @enderror
                </div>

                <div class="veld">
                    <label for="wensen">Wensen (bijvoorbeeld vegetarisch, halal, budget)</label>
                    <textarea id="wensen" name="wensen" rows="3" maxlength="500" style="min-height: 48px;">{{ old('wensen') }}</textarea>
                    @error('wensen') <div class="fout">{{ $message }}</div> @enderror
                </div>

                <div class="breed">
                    <button type="submit" class="knop">Advies vragen</button>
                </div>
            </form>

            @if (session('advies'))
                <div class="advies-resultaat">
                    <strong>Ons advies</strong>
                    <p>{!! nl2br(e(session('advies'))) !!}</p>
                    <p class="muted">Dit advies is gemaakt door een AI en kan fouten bevatten. Twijfel je of heb je een allergie? Neem dan contact met ons op.</p>
                </div>
            @endif
        </div>
    </section>

    @if ($pakketten->isEmpty())
        <p>Er zijn nog geen pakketten.</p>
    @else
        <div class="kaarten">
            @foreach ($pakketten as $pakket)
                <x-pakket-kaart :pakket="$pakket" />
            @endforeach
        </div>
    @endif
@endsection
