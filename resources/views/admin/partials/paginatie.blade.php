{{-- Eenvoudige paginering: 20 per pagina, de zoekopdracht blijft bewaard (withQueryString) --}}
@if ($lijst->hasPages())
    <nav class="paginatie" aria-label="Pagina's">
        @if ($lijst->onFirstPage())
            <span class="knop knop-klein knop-licht" aria-disabled="true">Vorige</span>
        @else
            <a class="knop knop-klein knop-licht" href="{{ $lijst->previousPageUrl() }}" rel="prev">Vorige</a>
        @endif

        <span>Pagina {{ $lijst->currentPage() }} van {{ $lijst->lastPage() }} ({{ $lijst->total() }} in totaal)</span>

        @if ($lijst->hasMorePages())
            <a class="knop knop-klein knop-licht" href="{{ $lijst->nextPageUrl() }}" rel="next">Volgende</a>
        @else
            <span class="knop knop-klein knop-licht" aria-disabled="true">Volgende</span>
        @endif
    </nav>
@endif
