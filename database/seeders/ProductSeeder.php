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
        ]);

        Product::create([
            'naam' => 'Lunchpakketten',
            'beschrijving' => 'Compleet en smakelijk',
            'prijs' => 8.50,
        ]);

        Product::create([
            'naam' => 'Catering op maat',
            'beschrijving' => 'Voor bedrijven en evenementen',
            'prijs' => 12.50,
        ]);
    }
}