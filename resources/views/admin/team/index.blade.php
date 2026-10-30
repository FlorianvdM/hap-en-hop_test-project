@extends('layouts.admin')

@section('title', 'Wie zijn wij | Hap & Hop')
@section('paginatitel', 'Wie zijn wij')
@section('subtitel')
    Zichtbare personen staan op de pagina "Wie zijn wij", gesorteerd op volgorde (laagste nummer eerst).
@endsection

@section('acties')
    <a class="knop" href="{{ route('admin.team.create') }}">Opdrachtgever toevoegen</a>
@endsection

@section('content')
    @if ($teamleden->isEmpty())
        <p>Er zijn nog geen opdrachtgevers toegevoegd.</p>
    @else
        <div class="tabel-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Foto</th>
                        <th>Naam</th>
                        <th>Functie</th>
                        <th>Volgorde</th>
                        <th>Website</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($teamleden as $teamlid)
                        <tr>
                            <td>
                                @if ($teamlid->fotoUrl())
                                    <img src="{{ $teamlid->fotoUrl() }}" alt="" width="48" height="48"
                                         style="border-radius: 50%; object-fit: cover;">
                                @else
                                    <span class="muted">Geen foto</span>
                                @endif
                            </td>
                            <td>{{ $teamlid->naam }}</td>
                            <td>{{ $teamlid->functie }}</td>
                            <td>{{ $teamlid->volgorde }}</td>
                            <td>
                                <span class="status {{ $teamlid->actief ? 'status-zichtbaar' : 'status-verborgen' }}">
                                    {{ $teamlid->actief ? 'Zichtbaar' : 'Verborgen' }}
                                </span>
                            </td>
                            <td>
                                <div class="acties">
                                    <a class="knop knop-klein knop-licht" href="{{ route('admin.team.edit', $teamlid) }}">Wijzigen</a>
                                    <form method="POST"
                                          action="{{ route('admin.team.destroy', $teamlid) }}"
                                          data-bevestig-titel="Opdrachtgever verwijderen?"
                                          data-bevestig="Weet je zeker dat je deze persoon wilt verwijderen?">
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
