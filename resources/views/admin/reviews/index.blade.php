@extends('layouts.admin')

@section('title', 'Reviews | Hap &amp; Hop')

@section('content')
    <div class="kop-met-knop">
        <h1>Reviews</h1>
        <a class="knop" href="{{ route('admin.reviews.create') }}">Review toevoegen</a>
    </div>

    <p class="muted" style="margin-bottom: 20px;">
        Zichtbare reviews staan onder "Wat klanten zeggen" op de homepage. Er worden maximaal 6 getoond, de nieuwste eerst.
    </p>

    @if ($reviews->isEmpty())
        <p>Er zijn nog geen reviews.</p>
    @else
        <div class="tabel-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Naam</th>
                        <th>Beoordeling</th>
                        <th>Review</th>
                        <th>Homepage</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($reviews as $review)
                        <tr>
                            <td>{{ $review->naam }}</td>
                            <td>{{ str_repeat('★', $review->beoordeling) }}{{ str_repeat('☆', 5 - $review->beoordeling) }}</td>
                            <td>{{ \Illuminate\Support\Str::limit($review->tekst, 80) }}</td>
                            <td>
                                <span class="status {{ $review->actief ? 'status-zichtbaar' : 'status-verborgen' }}">
                                    {{ $review->actief ? 'Zichtbaar' : 'Verborgen' }}
                                </span>
                            </td>
                            <td>
                                <div class="acties">
                                    <a class="knop knop-klein knop-licht" href="{{ route('admin.reviews.edit', $review) }}">Wijzigen</a>
                                    <form method="POST"
                                          action="{{ route('admin.reviews.destroy', $review) }}"
                                          data-bevestig-titel="Review verwijderen?"
                                          data-bevestig="Weet je zeker dat je deze review wilt verwijderen?">
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