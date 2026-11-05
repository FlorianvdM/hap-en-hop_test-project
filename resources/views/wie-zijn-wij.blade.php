@extends('layouts.app')

@section('title', 'Wie zijn wij | Hap & Hop')
@section('beschrijving', 'Maak kennis met de mensen achter Hap & Hop, verse broodjes en catering in Houten.')

@section('kop')
    <x-pagina-kop titel="Wie zijn wij">
        Maak kennis met de mensen achter Hap &amp; Hop.
    </x-pagina-kop>
@endsection

@section('content')
    @if ($teamleden->isEmpty())
        <p>We stellen ons binnenkort aan je voor.</p>
    @else
        <div class="team">
            @foreach ($teamleden as $teamlid)
                <article class="team-kaart">
                    @if ($teamlid->fotoUrl())
                        <img class="team-foto" src="{{ $teamlid->fotoUrl() }}" alt="Foto van {{ $teamlid->naam }}"
                            loading="lazy" style="object-fit: cover;">
                    @else
                        <div class="team-foto" role="img" aria-label="Foto volgt"><x-icon name="user" :size="48" /></div>
                    @endif

                    <h3>{{ $teamlid->naam }}</h3>

                    @if ($teamlid->functie)
                        <p class="team-functie">{{ $teamlid->functie }}</p>
                    @endif

                    @if ($teamlid->biografie)
                        <p class="muted">{!! nl2br(e($teamlid->biografie)) !!}</p>
                    @endif
                </article>
            @endforeach
        </div>
    @endif
@endsection
