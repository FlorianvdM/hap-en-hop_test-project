{{ $bestelling->leveringTekst() }}

@if ($bestelling->bezorgadres())
    <details class="adres-details">
        <summary class="knop knop-klein knop-licht">Adres</summary>
        <address>{!! nl2br(e($bestelling->bezorgadres())) !!}</address>
    </details>
@endif
