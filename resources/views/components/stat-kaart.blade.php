@props(['icon', 'label', 'waarde'])

<div class="stat-kaart">
    <div class="stat-icoon">
        <x-icon :name="$icon" :size="24" />
    </div>
    <div>
        <span class="stat-label">{{ $label }}</span>
        <span class="stat-waarde">{{ $waarde }}</span>
    </div>
</div>