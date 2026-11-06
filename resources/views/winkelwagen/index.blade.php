@extends('layouts.app')

@section('title', 'Winkelwagen | Hap & Hop')

@section('kop')
    <x-pagina-kop titel="Winkelwagen">
        Controleer je bestelling en pas de aantallen aan voordat je bestelt.
    </x-pagina-kop>
@endsection

@section('content')
    @if ($regels->isEmpty())
        <p style="margin-bottom: 16px;">Je winkelwagen is leeg.</p>
        <a class="knop" href="{{ route('pakketten.index') }}">Bekijk onze pakketten</a>
    @else
        <div class="raster-zij">
            <div>
                <div class="regels">
                    @foreach ($regels as $regel)
                        @php
                            // Een regel is een pakket of een product; de routes verschillen per soort
                            $isProduct = $regel->soort === 'product';
                            $wijzigUrl = $isProduct
                                ? route('winkelwagen.product.wijzigen', $regel->item)
                                : route('winkelwagen.wijzigen', $regel->item);
                            $verwijderUrl = $isProduct
                                ? route('winkelwagen.product.verwijderen', $regel->item)
                                : route('winkelwagen.verwijderen', $regel->item);
                            $veldId = 'aantal-' . $regel->soort . '-' . $regel->item->id;
                        @endphp

                        <div class="regel">
                            @if ($regel->item->afbeeldingUrl())
                                <div class="afbeelding" style="background-image: url('{{ $regel->item->afbeeldingUrl() }}')"></div>
                            @else
                                <div class="afbeelding"><x-icon name="utensils" :size="30" /></div>
                            @endif

                            <div>
                                <strong>{{ $regel->item->naam }}</strong>
                                <span class="muted" style="display: block; font-size: 14px;">
                                    &euro; {{ number_format($regel->item->prijs, 2, ',', '.') }} per
                                    {{ $isProduct ? 'stuk' : 'pakket' }}
                                </span>
                            </div>

                            {{-- Het aantal wordt automatisch opgeslagen zodra je het verandert --}}
                            <form method="POST" action="{{ $wijzigUrl }}">
                                @csrf
                                @method('PATCH')
                                <label class="sr-only" for="{{ $veldId }}">Aantal</label>
                                <input type="number" class="aantal" id="{{ $veldId }}" name="aantal" min="1" max="100"
                                    value="{{ $regel->aantal }}" data-auto-verstuur>
                                <noscript><button type="submit" class="knop knop-klein knop-licht">Bijwerken</button></noscript>
                            </form>

                            <div class="som">&euro; {{ number_format($regel->totaal, 2, ',', '.') }}</div>

                            <form method="POST" action="{{ $verwijderUrl }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="link-verwijder">Verwijderen</button>
                            </form>
                        </div>
                    @endforeach
                </div>

                <div class="hero-knoppen" style="margin-top: 28px;">
                    <a class="knop knop-licht" href="{{ route('pakketten.index') }}">Verder winkelen</a>
                </div>
            </div>

            <aside class="samenvatting">
                <h2>Overzicht</h2>

                <ul>
                    @foreach ($regels as $regel)
                        <li>
                            <span>{{ $regel->aantal }}&times; {{ $regel->item->naam }}</span>
                            <span>&euro; {{ number_format($regel->totaal, 2, ',', '.') }}</span>
                        </li>
                    @endforeach
                </ul>

                <div class="totaal">
                    <span>Totaal</span>
                    <span>&euro; {{ number_format($totaal, 2, ',', '.') }}</span>
                </div>

                <a class="knop knop-vol" href="{{ route('bestellen.create') }}">Bestellen</a>
                <p class="notitie">Laat je bezorgen, dan komt er &euro; {{ number_format(config('hapenhop.bezorgkosten'), 2, ',', '.') }} bezorgkosten bij.
                    Je betaalt bij ophalen of bezorgen: pin, contant of op factuur.</p>
            </aside>
        </div>
    @endif
@endsection
