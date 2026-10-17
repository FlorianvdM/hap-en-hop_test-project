@extends('layouts.app')

@section('title', 'Pakketten | Hap &amp; Hop')

@section('content')
    <x-pagina-kop titel="Onze pakketten">
        Kies het pakket dat bij jou past. Al onze pakketten zijn vers bereid en met zorg samengesteld.
    </x-pagina-kop>

    <section class="pakket-advies">
        <h2>Weet je niet welk pakket past?</h2>
        <p class="muted">Vertel hoeveel personen je verwacht en waar je op let. Je krijgt een advies van onze <strong>AI-assistent</strong>. Vul geen persoonsgegevens in.</p>

        <form class="formulier" method="POST" action="{{ route('pakketten.advies') }}">
            @csrf
            <label for="aantal">Aantal personen</label>
            <input type="number" id="aantal" name="aantal" min="1" max="500" value="{{ old('aantal') }}" required>
            @error('aantal') <div class="fout">{{ $message }}</div> @enderror

            <label for="wensen">Wensen (bijvoorbeeld vegetarisch, halal, budget)</label>
            <textarea id="wensen" name="wensen" rows="3" maxlength="500">{{ old('wensen') }}</textarea>
            @error('wensen') <div class="fout">{{ $message }}</div> @enderror

            <button type="submit" class="knop">Advies vragen</button>
        </form>

        @if (session('advies'))
            <div class="advies-resultaat">
                <strong>Ons advies</strong>
                <p>{!! nl2br(e(session('advies'))) !!}</p>
                <p class="muted">Dit advies is gemaakt door een AI en kan fouten bevatten. Twijfel je of heb je een allergie? Neem dan contact met ons op.</p>
            </div>
        @endif
    </section>

    <div class="pakket-lijst">
        @forelse ($pakketten as $pakket)
            <x-pakket-kaart :pakket="$pakket" />
        @empty
            <p>Er zijn nog geen pakketten.</p>
        @endforelse
    </div>
@endsection