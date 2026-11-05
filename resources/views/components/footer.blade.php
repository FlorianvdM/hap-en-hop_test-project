@php
    // Gegevens uit config/hapenhop.php (en dus uit .env)
    $tiktok = config('hapenhop.tiktok');
    $instagram = config('hapenhop.instagram');
    $facebook = config('hapenhop.facebook');
    $email = config('hapenhop.email');
    $telefoon = config('hapenhop.telefoon');
    $telefoonLink = $telefoon ? preg_replace('/[^+\d]/', '', $telefoon) : null;
    $kvk = config('hapenhop.kvk');
@endphp

<footer class="site-footer">
    <div class="wrap">
        <div class="footer-raster">
            <div>
                <img src="{{ asset('images/logo-volledig.png') }}" alt="Hap &amp; Hop">
                <p>Verse broodjes, pakketten en catering in Houten en omgeving.</p>

                {{-- Sociale iconen: alleen de links die zijn ingesteld --}}
                @if ($tiktok || $instagram || $facebook)
                    <div class="social">
                        @if ($tiktok)
                            <a href="{{ $tiktok }}" target="_blank" rel="noopener noreferrer"
                                aria-label="Hap &amp; Hop op TikTok">
                                <x-icon name="tiktok" :size="20" />
                            </a>
                        @endif

                        @if ($instagram)
                            <a href="{{ $instagram }}" target="_blank" rel="noopener noreferrer"
                                aria-label="Hap &amp; Hop op Instagram">
                                <x-icon name="instagram" :size="20" />
                            </a>
                        @endif

                        @if ($facebook)
                            <a href="{{ $facebook }}" target="_blank" rel="noopener noreferrer"
                                aria-label="Hap &amp; Hop op Facebook">
                                <x-icon name="facebook" :size="20" />
                            </a>
                        @endif
                    </div>
                @endif
            </div>

            <div>
                <h4>Bestellen</h4>
                <ul>
                    <li><a href="{{ route('pakketten.index') }}">Pakketten</a></li>
                    <li><a href="{{ route('producten') }}">Broodjes</a></li>
                    <li><a href="{{ route('catering') }}">Catering</a></li>
                    <li><a href="{{ route('winkelwagen.index') }}">Winkelwagen</a></li>
                    @auth
                        <li><a href="{{ route('mijn-bestellingen') }}">Mijn bestellingen</a></li>
                    @endauth
                </ul>
            </div>

            <div>
                <h4>Over ons</h4>
                <ul>
                    <li><a href="{{ route('wie-zijn-wij') }}">Wie zijn wij</a></li>
                    <li><a href="{{ route('info') }}">Info</a></li>
                    <li><a href="{{ route('contact') }}">Contact</a></li>
                    <li><a href="{{ route('privacy') }}">Privacyverklaring</a></li>
                    <li><a href="{{ route('voorwaarden') }}">Algemene voorwaarden</a></li>
                </ul>
            </div>

            <div>
                <h4>Contact</h4>
                <ul>
                    <li><a href="mailto:{{ $email }}">{{ $email }}</a></li>
                    {{-- Het telefoonnummer staat er alleen als het is ingesteld --}}
                    @if ($telefoon)
                        <li><a href="tel:{{ $telefoonLink }}">{{ $telefoon }}</a></li>
                    @endif
                    <li>{{ config('hapenhop.adres') }}</li>
                </ul>
            </div>
        </div>

        <div class="footer-onder">
            <span>&copy; {{ date('Y') }} Hap &amp; Hop</span>
            {{-- Het KvK-nummer verschijnt pas als het is ingevuld --}}
            @if ($kvk)
                <span>KvK {{ $kvk }}</span>
            @endif
        </div>
    </div>
</footer>
