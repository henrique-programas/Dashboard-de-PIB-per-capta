<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
    Schema::create('paises', function (Blueprint $table) {
        $table->id();
        $table->string('nome');           // Ex: Brasil
        $table->string('codigo', 3);      // Ex: BRA
        $table->string('regiao');         // Ex: América Latina
        $table->integer('ano');           // Ex: 2022
        $table->decimal('pib_per_capita', 12, 2)->nullable(); // Ex: 9673.45
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paises');
    }
};
