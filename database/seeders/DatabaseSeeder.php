<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $seeders = [
            CategoriaGastoSeeder::class,
            ServicioSeeder::class,
            CategoriaNotaSeeder::class,
        ];

        $this->call(array_values(array_filter($seeders, 'class_exists')));
    }
}
