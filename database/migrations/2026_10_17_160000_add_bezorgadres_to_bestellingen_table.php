<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bestellingen', function (Blueprint $table) {
            $table->string('straat', 150)->nullable()->after('levering');
            $table->string('postcode', 10)->nullable()->after('straat');
            $table->string('plaats', 100)->nullable()->after('postcode');
        });
    }

    public function down(): void
    {
        Schema::table('bestellingen', function (Blueprint $table) {
            $table->dropColumn(['straat', 'postcode', 'plaats']);
        });
    }
};
