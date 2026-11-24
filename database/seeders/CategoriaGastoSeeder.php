<?php

namespace Database\Seeders;

use App\Models\CategoriaGasto;
use Illuminate\Database\Seeder;

class CategoriaGastoSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Alimentación', 'Transporte', 'Servicios', 'Salud', 'Educación', 'Entretenimiento', 'Hogar', 'Otros'] as $nombre) {
            CategoriaGasto::firstOrCreate(['nombre' => $nombre]);
        }
    }
}
