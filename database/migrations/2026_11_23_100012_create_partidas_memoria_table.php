<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('partidas_memoria', function (Blueprint $table) {
            $table->id();
            $table->string('jugador', 40);
            $table->string('nivel', 10);
            $table->unsignedSmallInteger('movimientos');
            $table->unsignedSmallInteger('segundos');
            $table->unsignedInteger('puntaje');
            $table->timestamps();

            $table->index(['nivel', 'puntaje']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partidas_memoria');
    }
};
