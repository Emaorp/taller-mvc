<?php

namespace Tests\Feature;

use App\Models\Evento;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventosTest extends TestCase
{
    use RefreshDatabase;

    public function test_crea_edita_y_elimina_un_evento(): void
    {
        $this->post('/eventos/store', ['titulo' => 'Parcial', 'fecha' => '2026-11-25', 'hora' => '08:00', 'recordatorio_min' => 30])
             ->assertRedirect('/eventos?mes=2026-11');
        $evento = Evento::first();
        $this->assertSame(30, $evento->recordatorio_min);

        $this->put('/eventos/update', ['evento_id' => $evento->id, 'titulo' => 'Parcial final', 'fecha' => '2026-11-26']);
        $this->assertSame('Parcial final', $evento->fresh()->titulo);
        $this->assertNull($evento->fresh()->recordatorio_min);

        $this->delete("/eventos/delete/{$evento->id}");
        $this->assertDatabaseCount('eventos', 0);
    }

    public function test_el_recordatorio_exige_la_hora(): void
    {
        $this->post('/eventos/store', ['titulo' => 'Sin hora', 'fecha' => '2026-11-25', 'recordatorio_min' => 10])
             ->assertSessionHasErrors('hora');
    }

    public function test_el_calendario_muestra_los_eventos_del_mes(): void
    {
        Evento::create(['titulo' => 'Entrega taller', 'fecha' => '2020-09-10', 'hora' => '23:00']);

        $this->get('/eventos?mes=2020-09')->assertOk()->assertSee('Entrega taller')->assertSee('septiembre 2020');
        $this->get('/eventos?mes=2020-10')->assertOk()->assertDontSee('Entrega taller');
    }

    public function test_proximos_devuelve_solo_eventos_con_recordatorio_cercano(): void
    {
        $en20 = now()->addMinutes(20);

        Evento::create(['titulo' => 'Pronto', 'fecha' => $en20->toDateString(), 'hora' => $en20->format('H:i'), 'recordatorio_min' => 30]);
        Evento::create(['titulo' => 'Sin recordatorio', 'fecha' => $en20->toDateString(), 'hora' => $en20->format('H:i')]);
        Evento::create(['titulo' => 'Muy lejano', 'fecha' => now()->addDays(10)->toDateString(), 'hora' => '10:00', 'recordatorio_min' => 30]);

        $json = $this->getJson('/eventos/proximos')->assertOk()->json();

        $this->assertCount(1, $json);
        $this->assertSame('Pronto', $json[0]['titulo']);
        $this->assertSame(30, $json[0]['recordatorio_min']);
    }
}
