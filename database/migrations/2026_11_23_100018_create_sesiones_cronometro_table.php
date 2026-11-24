<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sesiones_cronometro', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 60);
            $table->unsignedBigInteger('duracion_ms');
            $table->text('vueltas')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sesiones_cronometro');
    }
};
