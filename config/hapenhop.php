<?php

return [
    'naam' => 'Hap & Hop',
    'email' => 'Haphopcatering@outlook.com',
    'tiktok' => 'https://www.tiktok.com/@haphopcatering',
    'instagram' => 'https://www.instagram.com/haphopcatering',

    // Naar dit adres gaat de melding bij een nieuwe bestelling
    'bestelmelding_naar' => 'Haphopcatering@outlook.com',

    // Aantal seconden wachten tussen de twee bestelmails. Standaard 0 (niet wachten).
    // Zet HAPHOP_MAIL_PAUZE=11 in .env als je met de gratis Mailtrap-sandbox test.
    'mail_pauze' => (int) env('HAPHOP_MAIL_PAUZE', 0),
];