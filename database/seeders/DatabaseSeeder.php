<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Voorbeeldpakketten en -producten zijn alleen voor lokaal en voor de tests.
        // Op de echte website vullen de beheerders hun eigen pakketten in via het beheerpaneel.
        if (! app()->isProduction()) {
            $this->call([
                PakketSeeder::class,
                ProductSeeder::class,
            ]);
        }

        // Het wachtwoord komt uit .env (HAPHOP_ADMIN_WACHTWOORD), nooit uit de code.
        $wachtwoord = config('hapenhop.admin_wachtwoord');

        // Alleen lokaal en in de tests mag er een standaardwachtwoord zijn.
        if (blank($wachtwoord) && ! app()->isProduction()) {
            $wachtwoord = 'Wijzig-mij-123';
        }

        // Op de echte website maken we geen beheerder zonder eigen wachtwoord.
        if (blank($wachtwoord)) {
            $this->command?->warn('Geen beheerder aangemaakt: zet HAPHOP_ADMIN_WACHTWOORD in .env.');

            return;
        }

        $admin = User::firstOrNew(['email' => config('hapenhop.admin_email')]);

        // Een bestaande beheerder laten we met rust, zodat een nieuw wachtwoord niet wordt overschreven.
        if (! $admin->exists) {
            $admin->name = 'Beheerder';
            $admin->password = $wachtwoord;
            $admin->is_admin = true;
            $admin->save();
        }
    }
}