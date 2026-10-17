@extends('layouts.app')

@section('title', 'Wie zijn wij | Hap &amp; Hop')

@section('content')
    <x-pagina-kop titel="Wie zijn wij">
        Maak kennis met de mensen achter Hap &amp; Hop.
    </x-pagina-kop>

    @if ($teamleden->isEmpty())
        <p>We stellen ons binnenkort aan je voor.</p>
    @else
        <div class="team-lijst">
            @foreach ($teamleden as $teamlid)
                <article class="team-kaart">
                    @if ($teamlid->fotoUrl())
                        <img class="team-foto" src="{{ $teamlid->fotoUrl() }}" alt="Foto van {{ $teamlid->naam }}"
                             loading="lazy">
                    @else
                        <div class="team-foto team-foto-leeg" role="img" aria-label="Foto volgt"></div>
                    @endif

                    <h2>{{ $teamlid->naam }}</h2>

                    @if ($teamlid->functie)
                        <p class="team-functie">{{ $teamlid->functie }}</p>
                    @endif

                    @if ($teamlid->biografie)
                        <p>{!! nl2br(e($teamlid->biografie)) !!}</p>
                    @endif
                </article>
            @endforeach
        </div>
    @endif
@endsection
