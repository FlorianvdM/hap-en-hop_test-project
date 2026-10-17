<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pakketten', function (Blueprint $table) {
            $table->string('afbeelding')->nullable()->after('beschrijving');
        });

        Schema::table('producten', function (Blueprint $table) {
            $table->string('afbeelding')->nullable()->after('beschrijving');
        });
    }

    public function down(): void
    {
        Schema::table('pakketten', function (Blueprint $table) {
            $table->dropColumn('afbeelding');
        });

        Schema::table('producten', function (Blueprint $table) {
            $table->dropColumn('afbeelding');
        });
    }
};
