@php
    $tiktok = config('hapenhop.tiktok');
    $instagram = config('hapenhop.instagram');
@endphp

<footer class="site-footer">
    @if ($tiktok || $instagram)
        <div class="social">
            @if ($tiktok)
                <a href="{{ $tiktok }}" target="_blank" rel="noopener noreferrer" aria-label="Hap &amp; Hop op TikTok">
                    <x-icon name="tiktok" :size="20" />
                </a>
            @endif

            @if ($instagram)
                <a href="{{ $instagram }}" target="_blank" rel="noopener noreferrer" aria-label="Hap &amp; Hop op Instagram">
                    <x-icon name="instagram" :size="20" />
                </a>
            @endif
        </div>
    @endif

    <div>
        <a href="{{ route('privacy') }}">Privacyverklaring</a>
    </div>

    <div>&copy; {{ date('Y') }} Hap &amp; Hop</div>
</footer>