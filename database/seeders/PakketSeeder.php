<?php

namespace Database\Seeders;

use App\Models\Pakket;
use Illuminate\Database\Seeder;

class PakketSeeder extends Seeder
{
    public function run(): void
    {
        Pakket::create([
            'naam' => 'Broodjespakket',
            'beschrijving' => 'Een mix van verse broodjes met diverse beleg.',
            'prijs' => 29.50,
            'aantal_personen' => 5,
        ]);

        Pakket::create([
            'naam' => 'Borrelplank',
            'beschrijving' => 'Kaas, worst, olijven en dips.',
            'prijs' => 24.00,
            'aantal_personen' => 4,
        ]);

        Pakket::create([
            'naam' => 'Lunchbox',
            'beschrijving' => 'Per stuk: broodje, fruit en drinken.',
            'prijs' => 7.50,
            'aantal_personen' => 1,
        ]);
    }
}