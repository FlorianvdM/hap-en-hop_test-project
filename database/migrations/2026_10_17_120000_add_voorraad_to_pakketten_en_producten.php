<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Leeg (null) betekent: onbeperkt beschikbaar
        Schema::table('pakketten', function (Blueprint $table) {
            $table->unsignedInteger('voorraad')->nullable()->after('prijs');
        });

        Schema::table('producten', function (Blueprint $table) {
            $table->unsignedInteger('voorraad')->nullable()->after('prijs');
        });
    }

    public function down(): void
    {
        Schema::table('pakketten', function (Blueprint $table) {
            $table->dropColumn('voorraad');
        });

        Schema::table('producten', function (Blueprint $table) {
            $table->dropColumn('voorraad');
        });
    }
};
