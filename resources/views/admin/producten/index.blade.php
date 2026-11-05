@extends('layouts.admin')

@section('title', 'Producten | Hap & Hop')
@section('paginatitel', 'Producten')
@section('subtitel')
    Bestelbare producten staan op de pagina Broodjes en onder "Onze favorieten" op de homepage, met een vaste prijs en een knop "In winkelwagen". Producten die niet bestelbaar zijn staan niet op de website.
@endsection

@section('acties')
    <a class="knop knop-licht" href="{{ route('producten') }}" target="_blank" rel="noopener">Bekijk pagina Producten</a>
    <a class="knop" href="{{ route('admin.producten.create') }}">Product toevoegen</a>
@endsection

@section('content')
    @if ($producten->isEmpty())
        <p>Er zijn nog geen producten.</p>
    @else
        <div class="tabel-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Naam</th>
                        <th>Beschrijving</th>
                        <th>Prijs</th>
                        <th>Voorraad</th>
                        <th>Bestelbaar</th>
                        <th>Zichtbaar</th>
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
                            <td>{{ $product->bestelbaar ? 'Ja' : 'Nee' }}</td>
                            <td>
                                <span class="status {{ $product->actief ? 'status-zichtbaar' : 'status-verborgen' }}">
                                    {{ $product->actief ? 'Zichtbaar' : 'Verborgen' }}
                                </span>
                            </td>
                            <td>
                                <div class="acties">
                                    <a class="knop knop-klein knop-licht"
                                        href="{{ route('admin.producten.edit', $product) }}">Wijzigen</a>
                                    <form method="POST" action="{{ route('admin.producten.destroy', $product) }}"
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