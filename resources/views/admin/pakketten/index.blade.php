@extends('layouts.admin')

@section('title', 'Pakketten | Hap & Hop')
@section('paginatitel', 'Pakketten')
@section('subtitel', 'Pakketten die klanten kunnen bestellen. Verborgen pakketten staan niet op de website.')

@section('acties')
    <a class="knop" href="{{ route('admin.pakketten.create') }}">Pakket toevoegen</a>
@endsection

@section('content')
    @if ($pakketten->isEmpty())
        <p>Er zijn nog geen pakketten.</p>
    @else
        <div class="tabel-wrap">
            <table class="tabel-kaarten">
                <thead>
                    <tr>
                        <th>Naam</th>
                        <th>Inhoud</th>
                        <th>Prijs</th>
                        <th>Voorraad</th>
                        <th>Website</th>
                        <th>Bestellingen</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pakketten as $pakket)
                        <tr>
                            <td data-label="Naam"><strong>{{ $pakket->naam }}</strong></td>
                            <td data-label="Inhoud">{{ str_replace(["\r\n", "\n"], ', ', $pakket->beschrijving) }}</td>
                            <td data-label="Prijs">&euro; {{ number_format($pakket->prijs, 2, ',', '.') }}</td>
                            <td data-label="Voorraad">{{ $pakket->voorraadBeperkt() ? $pakket->voorraad : 'Onbeperkt' }}</td>
                            <td data-label="Website">
                                <span class="status {{ $pakket->actief ? 'status-zichtbaar' : 'status-verborgen' }}">
                                    {{ $pakket->actief ? 'Zichtbaar' : 'Verborgen' }}
                                </span>
                            </td>
                            <td data-label="Bestellingen">{{ $pakket->bestellingen_count }}</td>
                            <td data-label="">
                                <div class="acties">
                                    <a class="knop knop-klein knop-licht" href="{{ route('admin.pakketten.edit', $pakket) }}">Wijzigen</a>
                                    <form method="POST"
                                          action="{{ route('admin.pakketten.destroy', $pakket) }}"
                                          data-bevestig-titel="Pakket verwijderen?"
                                          data-bevestig="Weet je zeker dat je dit pakket wilt verwijderen?">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="knop knop-klein knop-gevaar">Verwijderen</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endsection
