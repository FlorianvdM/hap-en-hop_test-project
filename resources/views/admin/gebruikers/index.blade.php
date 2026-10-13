@extends('layouts.admin')

@section('title', 'Gebruikers | Hap &amp; Hop')

@section('content')
    <h1>Gebruikers</h1>
    <p class="muted" style="margin-bottom: 20px;">
        Alle accounts op de website. Met "Mailen" opent je e-mailprogramma met het adres al ingevuld.
        Een beheerder kun je pas verwijderen nadat je zijn of haar rol hebt gewijzigd naar Klant.
    </p>

    <div class="tabel-wrap">
        <table>
            <thead>
                <tr>
                    <th>Naam</th>
                    <th>E-mailadres</th>
                    <th>Rol</th>
                    <th>Aangemaakt</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($gebruikers as $gebruiker)
                    <tr>
                        <td>
                            {{ $gebruiker->name }}
                            @if ($gebruiker->is(auth()->user()))
                                <span class="muted">(jij)</span>
                            @endif
                        </td>
                        <td>{{ $gebruiker->email }}</td>
                        <td>
                            <span class="status {{ $gebruiker->is_admin ? 'rol-beheerder' : 'rol-klant' }}">
                                {{ $gebruiker->is_admin ? 'Beheerder' : 'Klant' }}
                            </span>
                        </td>
                        <td>{{ $gebruiker->created_at->format('d-m-Y') }}</td>
                        <td>
                            <div class="acties">
                                <a class="knop knop-klein"
                                   href="mailto:{{ $gebruiker->email }}?subject={{ rawurlencode('Bericht van Hap & Hop') }}">Mailen</a>

                                <a class="knop knop-klein knop-licht" href="{{ route('admin.gebruikers.edit', $gebruiker) }}">Wijzigen</a>

                                @if ($gebruiker->is_admin)
                                    <button type="button" class="knop knop-klein knop-gevaar" disabled
                                            title="Wijzig eerst de rol naar Klant">Verwijderen</button>
                                @else
                                    <form method="POST"
                                          action="{{ route('admin.gebruikers.destroy', $gebruiker) }}"
                                          data-bevestig-titel="Gebruiker verwijderen?"
                                          data-bevestig="Weet je zeker dat je deze gebruiker wilt verwijderen? De bestellingen van deze gebruiker blijven bewaard.">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="knop knop-klein knop-gevaar">Verwijderen</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection