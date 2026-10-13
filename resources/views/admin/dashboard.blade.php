@extends('layouts.app')

@section('title', 'Beheer | Hap &amp; Hop')

@section('content')
    <h1>Beheer</h1>

    <div class="statistieken">
        <div class="stat"><strong>{{ $nieuweBestellingen }}</strong><br>nieuwe bestellingen</div>
        <div class="stat"><strong>{{ $ongelezenBerichten }}</strong><br>ongelezen berichten</div>
    </div>

    <h2>Bestellingen</h2>

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
                                    <select name="status" onchange="this.form.submit()">
                                        @foreach (['nieuw', 'in behandeling', 'afgerond'] as $status)
                                            <option value="{{ $status }}" @selected($bestelling->status === $status)>
                                                {{ ucfirst($status) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <h2>Contactberichten</h2>

    @if ($berichten->isEmpty())
        <p>Er zijn nog geen berichten.</p>
    @else
        <div class="tabel-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Ontvangen op</th>
                        <th>Van</th>
                        <th>Bericht</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($berichten as $bericht)
                        <tr class="{{ $bericht->gelezen ? '' : 'nieuw' }}">
                            <td>{{ $bericht->created_at->format('d-m-Y H:i') }}</td>
                            <td>
                                {{ $bericht->naam }}<br>
                                <a href="mailto:{{ $bericht->email }}">{{ $bericht->email }}</a>
                            </td>
                            <td>{!! nl2br(e($bericht->bericht)) !!}</td>
                            <td>
                                @if ($bericht->gelezen)
                                    Gelezen
                                @else
                                    <form method="POST" action="{{ route('admin.berichten.gelezen', $bericht) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="knop">Gelezen</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endsection