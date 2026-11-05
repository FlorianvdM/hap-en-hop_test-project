<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Welke allergenen erin zitten, ingevuld door de beheerders (bijvoorbeeld "gluten, melk, ei")
        Schema::table('pakketten', function (Blueprint $table) {
            $table->string('allergenen', 255)->nullable()->after('beschrijving');
        });

        Schema::table('producten', function (Blueprint $table) {
            $table->string('allergenen', 255)->nullable()->after('beschrijving');
        });
    }

    public function down(): void
    {
        Schema::table('pakketten', function (Blueprint $table) {
            $table->dropColumn('allergenen');
        });

        Schema::table('producten', function (Blueprint $table) {
            $table->dropColumn('allergenen');
        });
    }
};
