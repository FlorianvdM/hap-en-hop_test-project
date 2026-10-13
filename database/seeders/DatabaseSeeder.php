<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PakketSeeder::class,
        ]);

        $admin = User::firstOrNew(['email' => 'admin@hapenhop.nl']);

        if (! $admin->exists) {
            $admin->name = 'Beheerder';
            $admin->password = 'Wijzig-mij-123';
            $admin->is_admin = true;
            $admin->save();
        }
    }
}