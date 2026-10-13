<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bestellingen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pakket_id')->constrained('pakketten')->restrictOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('naam');
            $table->string('email');
            $table->string('telefoon')->nullable();
            $table->unsignedInteger('aantal')->default(1);
            $table->date('gewenste_datum');
            $table->text('opmerking')->nullable();
            $table->string('status')->default('nieuw');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bestellingen');
    }
};