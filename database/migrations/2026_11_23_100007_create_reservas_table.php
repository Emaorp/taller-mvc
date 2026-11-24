<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('servicio_id')->constrained('servicios')->restrictOnDelete();
            $table->string('nombre', 80);
            $table->string('email', 100);
            $table->date('fecha');
            $table->string('hora', 5);
            $table->string('estado', 15)->default('Pendiente');
            $table->timestamps();

            $table->index(['servicio_id', 'fecha', 'hora']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservas');
    }
};
