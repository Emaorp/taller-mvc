<?php

namespace Database\Seeders;

use App\Models\CategoriaNota;
use Illuminate\Database\Seeder;

class CategoriaNotaSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Personal', 'Trabajo', 'Estudio', 'Ideas'] as $nombre) {
            CategoriaNota::firstOrCreate(['nombre' => $nombre]);
        }
    }
}
