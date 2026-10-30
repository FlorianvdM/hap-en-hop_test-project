@props(['titel'])

{{-- Paginakop over de volle breedte. Gebruik hem in @section('kop') van een pagina. --}}
<section class="pagina-kop">
    <div class="wrap">
        <h1>{{ $titel }}</h1>
        @if (! $slot->isEmpty())
            <p>{{ $slot }}</p>
        @endif
    </div>
</section>
