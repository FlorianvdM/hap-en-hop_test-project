<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        Product::create([
            'naam' => 'Luxe broodjes',
            'beschrijving' => 'Diverse soorten, vers belegd',
            'prijs' => 3.50,
            'bestelbaar' => true,
        ]);

        Product::create([
            // Niet bestelbaar: staat op de Catering-pagina met "Neem contact op"
            'naam' => 'Catering op maat',
            'beschrijving' => 'Voor bedrijven en evenementen',
            'prijs' => 12.50,
        ]);
    }
}