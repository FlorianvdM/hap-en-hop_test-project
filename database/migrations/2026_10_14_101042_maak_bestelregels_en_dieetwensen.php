<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bestelregels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bestelling_id')->constrained('bestellingen')->cascadeOnDelete();
            $table->foreignId('pakket_id')->constrained('pakketten')->restrictOnDelete();
            $table->string('pakket_naam');
            $table->unsignedInteger('aantal')->default(1);
            $table->decimal('prijs', 8, 2);
            $table->timestamps();
        });

        // Bestaande bestellingen (één pakket per bestelling) omzetten naar bestelregels
        $bestaand = DB::table('bestellingen')
            ->join('pakketten', 'pakketten.id', '=', 'bestellingen.pakket_id')
            ->select(
                'bestellingen.id as bestelling_id',
                'bestellingen.pakket_id',
                'bestellingen.aantal',
                'pakketten.naam as pakket_naam',
                'pakketten.prijs as prijs'
            )
            ->get();

        foreach ($bestaand as $rij) {
            DB::table('bestelregels')->insert([
                'bestelling_id' => $rij->bestelling_id,
                'pakket_id' => $rij->pakket_id,
                'pakket_naam' => $rij->pakket_naam,
                'aantal' => $rij->aantal,
                'prijs' => $rij->prijs,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        Schema::table('bestellingen', function (Blueprint $table) {
            $table->dropForeign(['pakket_id']);
            $table->dropColumn(['pakket_id', 'aantal']);
            $table->json('dieetwensen')->nullable()->after('gewenste_datum');
        });
    }

    public function down(): void
    {
        Schema::table('bestellingen', function (Blueprint $table) {
            $table->dropColumn('dieetwensen');
            $table->unsignedBigInteger('pakket_id')->nullable()->after('id');
            $table->unsignedInteger('aantal')->default(1)->after('pakket_id');
        });

        Schema::dropIfExists('bestelregels');
    }
};