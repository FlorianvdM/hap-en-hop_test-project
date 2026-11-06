<?php

return [
    'naam' => 'Hap & Hop',

    // Contactadres op de website en in de privacyverklaring. Pas aan in .env (HAPHOP_EMAIL).
    'email' => env('HAPHOP_EMAIL', 'info@haphop.nl'),
    'adres' => 'Sleepboot 2, 3991 CN Houten',
    // Gewone link naar Google Maps (geen ingebouwde kaart, dus geen cookies van Google)
    'route' => 'https://www.google.com/maps/search/?api=1&query=' . urlencode('Sleepboot 2, 3991 CN Houten'),
    // Adres in de QR-code (Instellingen in het beheer). Leeg = de pakketpagina op APP_URL.
    // Zet bij de livegang HAPHOP_QR_URL=https://<jouw-domein>/pakketten in .env.
    'qr_url' => env('HAPHOP_QR_URL'),

    'tiktok' => 'https://www.tiktok.com/@haphopcatering',
    'instagram' => 'https://www.instagram.com/haphopcatering',
    'facebook' => 'https://www.facebook.com/profile.php?id=61595299014970',

    // Zakelijk telefoonnummer op de website. Een ander nummer kan via HAPHOP_TELEFOON in .env.
    'telefoon' => env('HAPHOP_TELEFOON', '+31 6 10402693'),

    // KvK-nummer. Leeg laten zolang Hap & Hop niet is ingeschreven; dan toont de footer niets.
    // Zodra het bestaat zet je HAPHOP_KVK=12345678 in .env op de server.
    'kvk' => env('HAPHOP_KVK'),

    // Naar dit adres gaat de melding bij een nieuwe bestelling.
    // Staat HAPHOP_BESTELMELDING_NAAR niet in .env, dan gebruiken we het contactadres hierboven.
    'bestelmelding_naar' => env('HAPHOP_BESTELMELDING_NAAR', env('HAPHOP_EMAIL', 'Haphopcatering@outlook.com')),

    // Beheerder die de seeder aanmaakt (php artisan db:seed). Het wachtwoord staat alleen in .env.
    'admin_email' => env('HAPHOP_ADMIN_EMAIL', 'admin@hapenhop.nl'),
    'admin_wachtwoord' => env('HAPHOP_ADMIN_WACHTWOORD'),

    // Aantal seconden wachten tussen de twee bestelmails. Standaard 0 (niet wachten).
    // Zet HAPHOP_MAIL_PAUZE=11 in .env als je met de gratis Mailtrap-sandbox test.
    'mail_pauze' => (int) env('HAPHOP_MAIL_PAUZE', 0),

    // Mails op de achtergrond versturen (via de wachtrij), zodat de klant na het bestellen niet hoeft te wachten.
    // Zet HAPHOP_MAIL_WACHTRIJ=true in .env en laat 'php artisan queue:work' draaien.
    'mail_wachtrij' => (bool) env('HAPHOP_MAIL_WACHTRIJ', false),

    // Vaste bezorgkosten in euro's. Het echte bedrag is nog niet bekend: pas het hier aan of zet HAPHOP_BEZORGKOSTEN in .env.
    'bezorgkosten' => (float) env('HAPHOP_BEZORGKOSTEN', 2.50),
];