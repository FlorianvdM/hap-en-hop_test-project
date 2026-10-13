@props(['titel'])

<div class="pagina-kop">
    <h1>{{ $titel }}</h1>
    @if (! $slot->isEmpty())
        <p>{{ $slot }}</p>
    @endif
</div>