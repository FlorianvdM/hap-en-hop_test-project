<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teamleden', function (Blueprint $table) {
            $table->id();
            $table->string('naam');
            $table->string('functie')->nullable();
            $table->text('biografie')->nullable();
            $table->string('foto')->nullable();
            $table->unsignedSmallInteger('volgorde')->default(0);
            $table->boolean('actief')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teamleden');
    }
};
