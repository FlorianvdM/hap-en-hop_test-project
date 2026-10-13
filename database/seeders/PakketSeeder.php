<?php

namespace Database\Seeders;

use App\Models\Pakket;
use Illuminate\Database\Seeder;

class PakketSeeder extends Seeder
{
    public function run(): void
    {
        Pakket::create([
            'naam' => 'Standaard pakket',
            'beschrijving' => "5 broodjes naar keuze\nFruit\nSap",
            'prijs' => 12.50,
            'aantal_personen' => 1,
        ]);

        Pakket::create([
            'naam' => 'Luxe pakket',
            'beschrijving' => "6 broodjes naar keuze\nSalade\nFruit\nSap",
            'prijs' => 18.50,
            'aantal_personen' => 1,
        ]);

        Pakket::create([
            'naam' => 'Premium pakket',
            'beschrijving' => "10 broodjes naar keuze\nSalade\nZoetigheden\nSap & koffie/thee",
            'prijs' => 24.50,
            'aantal_personen' => 1,
        ]);
    }
}