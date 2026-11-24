<?php

namespace Tests\Feature;

use App\Models\PartidaMemoria;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MemoriaTest extends TestCase
{
    use RefreshDatabase;

    public function test_el_nivel_genera_la_cantidad_de_cartas(): void
    {
        $this->get('/memoria/jugar/facil')->assertOk()->assertSee('data-par', false);
        $this->assertSame(12, substr_count($this->get('/memoria/jugar/facil')->getContent(), 'class="carta"'));
        $this->assertSame(24, substr_count($this->get('/memoria/jugar/dificil')->getContent(), 'class="carta"'));
    }

    public function test_nivel_inexistente_redirige(): void
    {
        $this->get('/memoria/jugar/imposible')->assertRedirect('/memoria');
    }

    public function test_guarda_el_puntaje_calculado_en_el_servidor(): void
    {
        $this->post('/memoria/guardar', ['jugador' => 'Emanuel', 'nivel' => 'facil', 'movimientos' => 10, 'segundos' => 30])
             ->assertRedirect('/memoria');

        $this->assertSame(500, PartidaMemoria::first()->puntaje);
        $this->get('/memoria')->assertSee('Emanuel')->assertSee('500');
    }

    public function test_rechaza_partidas_imposibles(): void
    {
        $this->post('/memoria/guardar', ['jugador' => 'Tramposo', 'nivel' => 'facil', 'movimientos' => 3, 'segundos' => 5]);
        $this->assertDatabaseCount('partidas_memoria', 0);

        $this->post('/memoria/guardar', ['jugador' => '', 'nivel' => 'otro', 'movimientos' => 0, 'segundos' => 0])
             ->assertSessionHasErrors(['jugador', 'nivel', 'movimientos', 'segundos']);
    }
}
