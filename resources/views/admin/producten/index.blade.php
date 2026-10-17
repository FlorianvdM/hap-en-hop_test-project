@extends('layouts.admin')

@section('title', 'Producten | Hap &amp; Hop')

@section('content')
    <div class="kop-met-knop">
        <h1>Producten</h1>
        <div class="acties">
            <a class="knop knop-licht" href="{{ route('producten') }}" target="_blank" rel="noopener">Bekijk pagina Producten</a>
            <a class="knop" href="{{ route('admin.producten.create') }}">Product toevoegen</a>
        </div>
    </div>

    <p class="muted" style="margin-bottom: 20px;">
        Zichtbare producten staan op de pagina Producten en onder "Onze favorieten" op de homepage, met een "Vanaf"-prijs.
    </p>

    @if ($producten->isEmpty())
        <p>Er zijn nog geen producten.</p>
    @else
        <div class="tabel-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Naam</th>
                        <th>Beschrijving</th>
                        <th>Vanaf-prijs</th>
                        <th>Voorraad</th>
                        <th>Homepage</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($producten as $product)
                        <tr>
                            <td>{{ $product->naam }}</td>
                            <td>{{ $product->beschrijving }}</td>
                            <td>&euro; {{ number_format($product->prijs, 2, ',', '.') }}</td>
                            <td>{{ $product->voorraadBeperkt() ? $product->voorraad : 'Onbeperkt' }}</td>
                            <td>
                                <span class="status {{ $product->actief ? 'status-zichtbaar' : 'status-verborgen' }}">
                                    {{ $product->actief ? 'Zichtbaar' : 'Verborgen' }}
                                </span>
                            </td>
                            <td>
                                <div class="acties">
                                    <a class="knop knop-klein knop-licht" href="{{ route('admin.producten.edit', $product) }}">Wijzigen</a>
                                    <form method="POST"
                                          action="{{ route('admin.producten.destroy', $product) }}"
                                          data-bevestig-titel="Product verwijderen?"
                                          data-bevestig="Weet je zeker dat je dit product wilt verwijderen?">
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