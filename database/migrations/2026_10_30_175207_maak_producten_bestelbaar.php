<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Producten kunnen nu ook echt besteld worden (bijvoorbeeld losse broodjes).
        // Standaard blijft een product een etalage-item; de beheerder zet "bestelbaar" aan.
        Schema::table('producten', function (Blueprint $table) {
            $table->boolean('bestelbaar')->default(false);
        });

        // Stap 1: de koppeling met pakketten even losmaken, zodat de kolom aangepast kan worden
        Schema::table('bestelregels', function (Blueprint $table) {
            $table->dropForeign(['pakket_id']);
        });

        // Stap 2: een bestelregel hoort bij een pakket OF een product, dus pakket_id mag leeg zijn
        Schema::table('bestelregels', function (Blueprint $table) {
            $table->unsignedBigInteger('pakket_id')->nullable()->change();
        });

        // Stap 3: de koppeling met pakketten terugzetten en een koppeling met producten toevoegen.
        // restrictOnDelete: een pakket of product met bestellingen kan niet worden verwijderd.
        Schema::table('bestelregels', function (Blueprint $table) {
            $table->foreign('pakket_id')->references('id')->on('pakketten')->restrictOnDelete();
            $table->foreignId('product_id')->nullable()->after('pakket_id')
                ->constrained('producten')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('bestelregels', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropColumn('product_id');
        });

        Schema::table('producten', function (Blueprint $table) {
            $table->dropColumn('bestelbaar');
        });

        // pakket_id laten we leeg mogen blijven: terug naar verplicht kan niet als er productregels zijn
    }
};