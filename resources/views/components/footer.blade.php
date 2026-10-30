@php
    // Gegevens uit config/hapenhop.php
    $tiktok = config('hapenhop.tiktok');
    $instagram = config('hapenhop.instagram');
    $email = config('hapenhop.email');
    $kvk = config('hapenhop.kvk');
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

    {{-- Contactgegevens; het KvK-nummer verschijnt pas als het is ingevuld --}}
    <div>
        <a href="mailto:{{ $email }}">{{ $email }}</a>
        @if ($kvk)
            &middot; KvK {{ $kvk }}
        @endif
    </div>

    <div>
        <a href="{{ route('privacy') }}">Privacyverklaring</a>
    </div>

    <div>&copy; {{ date('Y') }} Hap &amp; Hop</div>
</footer>