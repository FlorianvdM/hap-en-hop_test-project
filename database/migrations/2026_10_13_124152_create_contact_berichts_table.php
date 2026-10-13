<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contact_berichten', function (Blueprint $table) {
            $table->id();
            $table->string('naam');
            $table->string('email');
            $table->text('bericht');
            $table->boolean('gelezen')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_berichten');
    }
};