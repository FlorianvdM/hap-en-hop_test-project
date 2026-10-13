@extends('layouts.admin')

@section('title', 'Berichten | Hap &amp; Hop')

@section('content')
    <h1>Berichten</h1>
    <p class="muted">
        Berichten uit het contactformulier. Ongelezen berichten zijn geel gemarkeerd. Verwijderen kan nadat je een bericht als gelezen hebt gemarkeerd.
    </p>

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
                                <div class="acties">
                                    @if ($bericht->gelezen)
                                        <form method="POST"
                                              action="{{ route('admin.berichten.verwijderen', $bericht) }}"
                                              onsubmit="return confirm('Weet je zeker dat je dit bericht wilt verwijderen?')">
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
    @endif
@endsection