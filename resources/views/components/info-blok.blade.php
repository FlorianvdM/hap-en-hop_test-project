@props(['icon', 'titel'])

<div class="info-blok">
    <div class="info-icoon">
        <x-icon :name="$icon" :size="32" />
    </div>
    <div class="info-tekst">
        <h3>{{ $titel }}</h3>
        <p>{{ $slot }}</p>
    </div>
</div>