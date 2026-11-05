@extends('layouts.admin')

@section('title', 'Berichten | Hap & Hop')
@section('paginatitel', 'Berichten')
@section('subtitel')
    Berichten uit het contactformulier. Ongelezen berichten zijn geel gemarkeerd. Verwijderen kan nadat je een bericht als
    gelezen hebt gemarkeerd.
@endsection

@section('content')
    <form class="zoek-balk" method="GET" action="{{ route('admin.berichten') }}">
        <div class="veld">
            <label for="zoek">Zoeken</label>
            <input type="search" id="zoek" name="zoek" value="{{ $zoek }}" placeholder="Naam, e-mailadres of tekst">
        </div>

        <div class="veld">
            <label for="filter">Tonen</label>
            <select id="filter" name="filter">
                <option value="">Alle berichten</option>
                <option value="ongelezen" @selected($filter === 'ongelezen')>Alleen ongelezen</option>
                <option value="catering" @selected($filter === 'catering')>Alleen cateringaanvragen</option>
            </select>
        </div>

        <button type="submit" class="knop">Zoeken</button>
        @if ($zoek !== '' || $filter)
            <a class="knop knop-licht" href="{{ route('admin.berichten') }}">Wissen</a>
        @endif
    </form>

    @if ($berichten->isEmpty())
        <p>{{ $zoek !== '' || $filter ? 'Geen berichten gevonden.' : 'Er zijn nog geen berichten.' }}</p>
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
                                <div class="acties">
                                    @if ($bericht->gelezen)
                                        <form method="POST" action="{{ route('admin.berichten.verwijderen', $bericht) }}"
                                            data-bevestig-titel="Bericht verwijderen?"
                                            data-bevestig="Weet je zeker dat je dit bericht wilt verwijderen?">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="knop knop-klein knop-gevaar">Verwijderen</button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('admin.berichten.gelezen', $bericht) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="knop knop-klein">Gelezen</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @include('admin.partials.paginatie', ['lijst' => $berichten])
    @endif
@endsection
