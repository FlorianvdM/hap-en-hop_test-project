@extends('layouts.admin')

@section('title', 'Bestellingen | Hap &amp; Hop')

@section('content')
    <h1>Bestellingen</h1>
    <p class="muted">
        Nieuwe bestellingen zijn geel gemarkeerd. Verwijder je een bestelling met status Verzonden, dan verdwijnt hij uit
        de lijst en telt hij mee in de omzet. Bestellingen met een andere status worden definitief verwijderd en tellen
        niet mee in de omzet.
    </p>

    @if ($bestellingen->isEmpty())
        <p>Er zijn nog geen bestellingen.</p>
    @else
        <div class="tabel-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Besteld op</th>
                        <th>Klant</th>
                        <th>Pakket</th>
                        <th>Aantal</th>
                        <th>Gewenste datum</th>
                        <th>Opmerking</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($bestellingen as $bestelling)
                        <tr class="{{ $bestelling->status === 'nieuw' ? 'nieuw' : '' }}">
                            <td>{{ $bestelling->created_at->format('d-m-Y H:i') }}</td>
                            <td>
                                {{ $bestelling->naam }}<br>
                                <a href="mailto:{{ $bestelling->email }}">{{ $bestelling->email }}</a>
                                @if ($bestelling->telefoon)
                                    <br>{{ $bestelling->telefoon }}
                                @endif
                            </td>
                            <td>{{ $bestelling->pakket->naam }}</td>
                            <td>{{ $bestelling->aantal }}</td>
                            <td>{{ $bestelling->gewenste_datum->format('d-m-Y') }}</td>
                            <td>{{ $bestelling->opmerking }}</td>
                            <td>
                                <form method="POST" action="{{ route('admin.bestellingen.status', $bestelling) }}">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status"
                                            class="status-select status-{{ str_replace(' ', '-', $bestelling->status) }}"
                                            onchange="this.form.submit()">
                                        @foreach (['nieuw', 'in behandeling', 'verzonden'] as $status)
                                            <option value="{{ $status }}" @selected($bestelling->status === $status)>
                                                {{ ucfirst($status) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </form>
                            </td>
                            <td>
                                <form method="POST"
                                      action="{{ route('admin.bestellingen.verwijderen', $bestelling) }}"
                                      @if ($bestelling->status !== 'verzonden')
                                          data-bevestig-titel="Bestelling verwijderen?"
                                          data-bevestig="Weet je zeker dat je deze bestelling wilt verwijderen? Hij wordt definitief verwijderd en telt niet meer mee in het totaal aantal bestellingen."
                                      @endif>
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="knop knop-klein knop-gevaar">Verwijderen</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endsection