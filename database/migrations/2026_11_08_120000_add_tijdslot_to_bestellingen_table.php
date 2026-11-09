<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bestellingen', function (Blueprint $table) {
            // Leeg bij oude bestellingen, die hadden nog geen tijdvak
            $table->string('tijdslot', 30)->nullable()->after('gewenste_datum');
        });
    }

    public function down(): void
    {
        Schema::table('bestellingen', function (Blueprint $table) {
            $table->dropColumn('tijdslot');
        });
    }
};
