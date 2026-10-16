@extends('layouts.admin')

@section('title', 'Bestellingen | Hap &amp; Hop')

@section('content')
    <h1>Bestellingen</h1>
    <p class="muted" style="margin-bottom: 20px;">
        Nieuwe bestellingen zijn geel gemarkeerd. Verwijder je een bestelling met status Verzonden, dan verdwijnt hij uit
        de lijst en telt hij mee in de omzet. Bestellingen met een andere status worden definitief verwijderd en tellen
        niet mee in de omzet.
    </p>

    <form class="export-balk" method="GET" action="{{ route('admin.bestellingen.export') }}">
        <label>
            Van (besteldatum)
            <input type="date" name="van" value="{{ request('van') }}">
        </label>

        <label>
            Tot en met
            <input type="date" name="tot" value="{{ request('tot') }}">
        </label>

        <button type="submit" class="knop">
            <x-icon name="download" :size="18" /> Exporteren (CSV)
        </button>

        <p class="export-uitleg muted">
            Laat de data leeg om alle bestellingen te exporteren. Het bestand bevat ook verzonden bestellingen die uit de
            lijst zijn gehaald en opent direct in Excel.
        </p>

        @error('van') <div class="fout" style="flex-basis: 100%;">{{ $message }}</div> @enderror
        @error('tot') <div class="fout" style="flex-basis: 100%;">{{ $message }}</div> @enderror
    </form>

    @if ($bestellingen->isEmpty())
        <p>Er zijn nog geen bestellingen.</p>
    @else
        <div class="tabel-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Besteld op</th>
                        <th>Klant</th>
                        <th>Bestelling</th>
                        <th>Totaal</th>
                        <th>Gewenste datum</th>
                        <th>Dieetwensen</th>
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
                            <td>
                                @foreach ($bestelling->regels as $regel)
                                    {{ $regel->aantal }}&times; {{ $regel->pakket_naam }}<br>
                                @endforeach
                            </td>
                            <td>&euro; {{ number_format($bestelling->totaal, 2, ',', '.') }}</td>
                            <td>{{ $bestelling->gewenste_datum->format('d-m-Y') }}</td>
                            <td>
                                @foreach (($bestelling->dieetwensen ?? []) as $wens)
                                    <span class="status dieet-label">{{ ucfirst($wens) }}</span>
                                @endforeach
                            </td>
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