@extends('layouts.app')

@section('title', 'Pakketten | Hap &amp; Hop')

@section('content')
    <x-pagina-kop titel="Onze pakketten">
        Kies het pakket dat bij jou past. Al onze pakketten zijn vers bereid en met zorg samengesteld.
    </x-pagina-kop>

    <div class="pakket-lijst">
        @forelse ($pakketten as $pakket)
            <x-pakket-kaart :pakket="$pakket" />
        @empty
            <p>Er zijn nog geen pakketten.</p>
        @endforelse
    </div>
@endsection