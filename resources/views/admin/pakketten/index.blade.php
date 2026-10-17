@extends('layouts.admin')

@section('title', 'Pakketten | Hap &amp; Hop')

@section('content')
    <div class="kop-met-knop">
        <h1>Pakketten</h1>
        <a class="knop" href="{{ route('admin.pakketten.create') }}">Pakket toevoegen</a>
    </div>

    <p class="muted" style="margin-bottom: 20px;">
        Pakketten die klanten kunnen bestellen. Verborgen pakketten staan niet op de website.
    </p>

    @if ($pakketten->isEmpty())
        <p>Er zijn nog geen pakketten.</p>
    @else
        <div class="tabel-wrap">
            <table>
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
                            <td>{{ $pakket->naam }}</td>
                            <td>{{ str_replace(["\r\n", "\n"], ', ', $pakket->beschrijving) }}</td>
                            <td>&euro; {{ number_format($pakket->prijs, 2, ',', '.') }}</td>
                            <td>{{ $pakket->voorraadBeperkt() ? $pakket->voorraad : 'Onbeperkt' }}</td>
                            <td>
                                <span class="status {{ $pakket->actief ? 'status-zichtbaar' : 'status-verborgen' }}">
                                    {{ $pakket->actief ? 'Zichtbaar' : 'Verborgen' }}
                                </span>
                            </td>
                            <td>{{ $pakket->bestellingen_count }}</td>
                            <td>
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