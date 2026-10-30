@props(['icon', 'titel'])

<div class="info-blok">
    <div class="info-icoon">
        <x-icon :name="$icon" :size="24" />
    </div>
    <div>
        <h3>{{ $titel }}</h3>
        <p>{{ $slot }}</p>
    </div>
</div>
