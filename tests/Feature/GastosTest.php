<?php

namespace Tests\Feature;

use App\Models\CategoriaGasto;
use App\Models\Gasto;
use Database\Seeders\CategoriaGastoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GastosTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CategoriaGastoSeeder::class);
    }

    private function crear(string $descripcion, float $monto, string $fecha, string $categoria = 'Alimentación'): Gasto
    {
        return Gasto::create([
            'descripcion' => $descripcion, 'monto' => $monto, 'fecha' => $fecha,
            'categoria_gasto_id' => CategoriaGasto::where('nombre', $categoria)->first()->id,
        ]);
    }

    public function test_registra_un_gasto(): void
    {
        $this->post('/gastos/store', [
            'descripcion' => 'Almuerzo', 'monto' => 25000, 'fecha' => '2026-11-10',
            'categoria_gasto_id' => CategoriaGasto::first()->id,
        ])->assertRedirect('/gastos?mes=2026-11');

        $this->assertDatabaseHas('gastos', ['descripcion' => 'Almuerzo']);
    }

    public function test_valida_el_gasto(): void
    {
        $this->post('/gastos/store', ['descripcion' => '', 'monto' => 0, 'fecha' => 'x', 'categoria_gasto_id' => 999])
             ->assertSessionHasErrors(['descripcion', 'monto', 'fecha', 'categoria_gasto_id']);
    }

    public function test_el_resumen_mensual_suma_por_categoria(): void
    {
        $this->crear('Almuerzo', 30000, '2026-11-05');
        $this->crear('Cena', 10000, '2026-11-06');
        $this->crear('Bus', 10000, '2026-11-07', 'Transporte');
        $this->crear('Gasto de otro mes', 99999, '2026-10-30');

        $this->get('/gastos/resumen?mes=2026-11')
             ->assertOk()
             ->assertSee('50.000,00')
             ->assertSee('Alimentación')
             ->assertSee('80%');
    }

    public function test_la_lista_solo_muestra_el_mes_filtrado(): void
    {
        $this->crear('Gasto de noviembre', 1000, '2026-11-05');
        $this->crear('Gasto de octubre', 2000, '2026-10-05');

        $this->get('/gastos?mes=2026-11')->assertSee('Gasto de noviembre')->assertDontSee('Gasto de octubre');
    }

    public function test_edita_y_elimina_un_gasto(): void
    {
        $gasto = $this->crear('Original', 1000, '2026-11-05');

        $this->put('/gastos/update', [
            'gasto_id' => $gasto->id, 'descripcion' => 'Editado', 'monto' => 2000, 'fecha' => '2026-11-05',
            'categoria_gasto_id' => $gasto->categoria_gasto_id,
        ]);
        $this->assertSame('Editado', $gasto->fresh()->descripcion);

        $this->delete("/gastos/delete/{$gasto->id}");
        $this->assertDatabaseMissing('gastos', ['id' => $gasto->id]);
    }
}
