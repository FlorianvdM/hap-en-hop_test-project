@extends('layouts.admin')

@section('title', 'Bestellingen | Hap & Hop')
@section('paginatitel', 'Bestellingen')
@section('subtitel', 'Nieuwe bestellingen zijn geel gemarkeerd.')

@section('content')
    {{-- Exporteren naar CSV (opent direct in Excel) --}}
    <form class="export-balk" method="GET" action="{{ route('admin.bestellingen.export') }}">
        <div class="veld">
            <label for="van">Van (besteldatum)</label>
            <input type="date" id="van" name="van" value="{{ request('van') }}">
        </div>

        <div class="veld">
            <label for="tot">Tot en met</label>
            <input type="date" id="tot" name="tot" value="{{ request('tot') }}">
        </div>

        <button type="submit" class="knop">
            <x-icon name="download" :size="18" /> Exporteren (CSV)
        </button>

        <p>
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
            <table class="tabel-kaarten">
                <thead>
                    <tr>
                        <th>Bestelling</th>
                        <th>Klant</th>
                        <th>Inhoud en wensen</th>
                        <th>Gewenst en levering</th>
                        <th>Totaal</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($bestellingen as $bestelling)
                        <tr class="{{ $bestelling->status === 'nieuw' ? 'nieuw' : '' }}">
                            <td data-label="Bestelling">
                                <div>
                                    <strong>#{{ $bestelling->id }}</strong>
                                    <span class="klein">{{ $bestelling->created_at->format('d-m-Y H:i') }}</span>
                                </div>
                            </td>

                            <td data-label="Klant">
                                <div>
                                    {{ $bestelling->naam }}
                                    <span class="klein"><a href="mailto:{{ $bestelling->email }}">{{ $bestelling->email }}</a></span>
                                    @if ($bestelling->telefoon)
                                        <span class="klein">{{ $bestelling->telefoon }}</span>
                                    @endif
                                </div>
                            </td>

                            <td data-label="Inhoud">
                                <div>
                                    @foreach ($bestelling->regels as $regel)
                                        {{ $regel->aantal }}&times; {{ $regel->pakket_naam }}<br>
                                    @endforeach

                                    @foreach (($bestelling->dieetwensen ?? []) as $wens)
                                        <span class="status dieet-label" style="margin: 6px 6px 0 0;">{{ ucfirst($wens) }}</span>
                                    @endforeach

                                    @if ($bestelling->opmerking)
                                        <span class="klein" style="margin-top: 6px;">Opmerking: {{ $bestelling->opmerking }}</span>
                                    @endif
                                </div>
                            </td>

                            <td data-label="Gewenst en levering">
                                <div>
                                    <strong>{{ $bestelling->gewenste_datum->format('d-m-Y') }}</strong>
                                    <div class="klein">@include('admin.partials.levering', ['bestelling' => $bestelling])</div>
                                </div>
                            </td>

                            <td data-label="Totaal">&euro; {{ number_format($bestelling->totaal, 2, ',', '.') }}</td>

                            <td data-label="Status">
                                <form method="POST" action="{{ route('admin.bestellingen.status', $bestelling) }}">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" aria-label="Status van bestelling #{{ $bestelling->id }}"
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

                            <td data-label="">
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

        <p class="muted" style="font-size: 14px; max-width: 760px;">
            Verwijder je een bestelling met status Verzonden, dan verdwijnt hij uit de lijst en telt hij mee in de omzet.
            Bestellingen met een andere status worden definitief verwijderd en tellen niet mee in de omzet.
        </p>
    @endif
@endsection
