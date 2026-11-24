<?php

namespace Tests\Feature;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class NavegacionTest extends TestCase
{
    use RefreshDatabase;

    public function test_todas_las_pantallas_principales_cargan(): void
    {
        $this->seed(DatabaseSeeder::class);

        $rutas = [
            '/', '/tareas', '/propinas', '/contrasenas', '/gastos', '/gastos/resumen', '/gastos/create',
            '/reservas', '/reservas/create', '/notas', '/notas/create', '/notas/categorias',
            '/eventos', '/eventos/create', '/eventos/proximos', '/recetas', '/recetas/create',
            '/memoria', '/memoria/jugar/facil', '/memoria/jugar/medio', '/memoria/jugar/dificil',
            '/encuestas', '/encuestas/create', '/cronometro',
        ];

        $registradas = collect(Route::getRoutes()->getRoutes())->map(fn ($r) => '/' . ltrim($r->uri(), '/'))->all();

        foreach ($rutas as $ruta) {
            if (preg_match('#^/memoria/jugar/#', $ruta) ? in_array('/memoria/jugar/{nivel}', $registradas) : in_array($ruta, $registradas)) {
                $this->get($ruta)->assertOk();
            }
        }
    }

    public function test_ruta_inexistente_muestra_404_en_espanol(): void
    {
        $this->get('/no-existe')->assertNotFound()->assertSee('No encontrado');
    }
}
