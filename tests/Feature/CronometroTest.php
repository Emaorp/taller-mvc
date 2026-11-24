<?php

namespace Tests\Feature;

use App\Models\SesionCronometro;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CronometroTest extends TestCase
{
    use RefreshDatabase;

    public function test_guarda_una_sesion_con_sus_vueltas(): void
    {
        $this->post('/cronometro/store', [
            'nombre' => 'Entrenamiento', 'duracion_ms' => 65250, 'vueltas' => json_encode([20000, 41000, 65250]),
        ])->assertRedirect('/cronometro');

        $sesion = SesionCronometro::first();
        $this->assertSame([20000, 41000, 65250], $sesion->vueltas);
        $this->get('/cronometro')->assertSee('Entrenamiento')->assertSee('01:05.25');
    }

    public function test_valida_la_sesion(): void
    {
        $this->post('/cronometro/store', ['nombre' => '', 'duracion_ms' => 0])
             ->assertSessionHasErrors(['nombre', 'duracion_ms']);

        $this->post('/cronometro/store', ['nombre' => 'x', 'duracion_ms' => 1000, 'vueltas' => '["a"]'])
             ->assertSessionHasErrors('vueltas');
    }

    public function test_elimina_una_sesion(): void
    {
        $sesion = SesionCronometro::create(['nombre' => 'x', 'duracion_ms' => 1000, 'vueltas' => []]);

        $this->delete("/cronometro/delete/{$sesion->id}");

        $this->assertDatabaseCount('sesiones_cronometro', 0);
    }
}
