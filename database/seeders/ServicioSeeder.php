<?php

namespace Database\Seeders;

use App\Models\Servicio;
use Illuminate\Database\Seeder;

class ServicioSeeder extends Seeder
{
    public function run(): void
    {
        $servicios = [
            ['Consulta general', 60, 'Atención inicial y diagnóstico'],
            ['Asesoría especializada', 60, 'Sesión con un especialista'],
            ['Revisión técnica', 60, 'Revisión y mantenimiento'],
        ];

        foreach ($servicios as [$nombre, $duracion, $descripcion]) {
            Servicio::firstOrCreate(['nombre' => $nombre], ['duracion_min' => $duracion, 'descripcion' => $descripcion]);
        }
    }
}
