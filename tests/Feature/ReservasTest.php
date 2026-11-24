<?php

namespace Tests\Feature;

use App\Models\Reserva;
use App\Models\Servicio;
use Database\Seeders\ServicioSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReservasTest extends TestCase
{
    use RefreshDatabase;

    private Servicio $servicio;
    private string $manana;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ServicioSeeder::class);
        $this->servicio = Servicio::first();
        $this->manana = now()->addDay()->toDateString();
    }

    private function datos(array $extra = []): array
    {
        return array_merge([
            'servicio_id' => $this->servicio->id, 'nombre' => 'Ana', 'email' => 'ana@test.com',
            'fecha' => $this->manana, 'hora' => '10:00',
        ], $extra);
    }

    public function test_crea_una_reserva_pendiente(): void
    {
        $this->post('/reservas/store', $this->datos())->assertRedirect('/reservas');

        $this->assertDatabaseHas('reservas', ['nombre' => 'Ana', 'hora' => '10:00', 'estado' => 'Pendiente']);
    }

    public function test_no_permite_reservar_una_hora_ocupada(): void
    {
        $this->post('/reservas/store', $this->datos());
        $this->post('/reservas/store', $this->datos(['nombre' => 'Otra persona']));

        $this->assertDatabaseCount('reservas', 1);
    }

    public function test_la_disponibilidad_marca_la_hora_ocupada(): void
    {
        $this->post('/reservas/store', $this->datos());

        $disp = Reserva::disponibilidad($this->servicio->id, $this->manana);

        $this->assertFalse($disp['10:00']);
        $this->assertTrue($disp['11:00']);
        $this->get("/reservas/create?servicio_id={$this->servicio->id}&fecha={$this->manana}")->assertSee('ocupada');
    }

    public function test_confirma_y_cancela_y_libera_la_hora(): void
    {
        $this->post('/reservas/store', $this->datos());
        $reserva = Reserva::first();

        $this->patch("/reservas/confirm/{$reserva->id}");
        $this->assertSame('Confirmada', $reserva->fresh()->estado);

        $this->patch("/reservas/cancel/{$reserva->id}");
        $this->assertSame('Cancelada', $reserva->fresh()->estado);

        $this->assertTrue(Reserva::disponibilidad($this->servicio->id, $this->manana)['10:00']);
    }

    public function test_valida_fecha_pasada_y_hora_invalida(): void
    {
        $this->post('/reservas/store', $this->datos(['fecha' => now()->subDay()->toDateString()]))
             ->assertSessionHasErrors('fecha');

        $this->post('/reservas/store', $this->datos(['hora' => '03:00']))->assertSessionHasErrors('hora');
    }

    public function test_elimina_una_reserva(): void
    {
        $this->post('/reservas/store', $this->datos());
        $reserva = Reserva::first();

        $this->delete("/reservas/delete/{$reserva->id}");

        $this->assertDatabaseCount('reservas', 0);
    }
}
