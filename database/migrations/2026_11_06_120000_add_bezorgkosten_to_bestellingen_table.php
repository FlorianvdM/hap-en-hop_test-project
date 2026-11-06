<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // De bezorgkosten van het moment van bestellen, net als de prijs in de bestelregels. Bij ophalen 0.
        Schema::table('bestellingen', function (Blueprint $table) {
            $table->decimal('bezorgkosten', 8, 2)->default(0)->after('plaats');
        });
    }

    public function down(): void
    {
        Schema::table('bestellingen', function (Blueprint $table) {
            $table->dropColumn('bezorgkosten');
        });
    }
};
