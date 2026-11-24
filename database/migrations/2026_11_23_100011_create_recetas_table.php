<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recetas', function (Blueprint $table) {
            $table->id();
            $table->string('titulo', 120);
            $table->string('tipo_comida', 20);
            $table->text('ingredientes');
            $table->text('preparacion');
            $table->unsignedSmallInteger('tiempo_min');
            $table->unsignedTinyInteger('porciones')->default(1);
            $table->boolean('guardada')->default(false);
            $table->string('token', 36)->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recetas');
    }
};
