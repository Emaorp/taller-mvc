<?php

namespace Tests\Feature;

use App\Models\Receta;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecetasTest extends TestCase
{
    use RefreshDatabase;

    private function receta(string $titulo, string $tipo, string $ingredientes): Receta
    {
        return Receta::create([
            'titulo' => $titulo, 'tipo_comida' => $tipo, 'ingredientes' => $ingredientes,
            'preparacion' => 'Mezclar y cocinar', 'tiempo_min' => 20, 'porciones' => 2,
        ]);
    }

    public function test_crea_edita_y_elimina_una_receta_con_token(): void
    {
        $this->post('/recetas/store', [
            'titulo' => 'Arepas', 'tipo_comida' => 'Desayuno', 'ingredientes' => "Maíz\nSal",
            'preparacion' => 'Amasar', 'tiempo_min' => 15, 'porciones' => 4,
        ])->assertRedirect('/recetas');
        $receta = Receta::first();
        $this->assertNotEmpty($receta->token);

        $this->put('/recetas/update', [
            'receta_id' => $receta->id, 'titulo' => 'Arepas rellenas', 'tipo_comida' => 'Almuerzo',
            'ingredientes' => 'Maíz', 'preparacion' => 'Amasar y rellenar', 'tiempo_min' => 25, 'porciones' => 4,
        ]);
        $this->assertSame('Arepas rellenas', $receta->fresh()->titulo);

        $this->delete("/recetas/delete/{$receta->id}");
        $this->assertDatabaseCount('recetas', 0);
    }

    public function test_filtra_por_texto_tipo_ingrediente_y_guardadas(): void
    {
        $a = $this->receta('Pasta con tomate', 'Almuerzo', "Pasta\nTomate");
        $this->receta('Tarta de manzana', 'Postre', "Harina\nManzana");

        $this->get('/recetas?filter=Pasta')->assertSee('Pasta con tomate')->assertDontSee('Tarta de manzana');
        $this->get('/recetas?tipo_comida=Postre')->assertSee('Tarta de manzana')->assertDontSee('Pasta con tomate');
        $this->get('/recetas?ingrediente=manzana')->assertSee('Tarta de manzana')->assertDontSee('Pasta con tomate');

        $this->patch("/recetas/guardar/{$a->id}");
        $this->assertTrue($a->fresh()->guardada);
        $this->get('/recetas?solo_guardadas=1')->assertSee('Pasta con tomate')->assertDontSee('Tarta de manzana');
    }

    public function test_el_enlace_compartido_muestra_la_receta(): void
    {
        $receta = $this->receta('Limonada', 'Bebida', "Limón\nAzúcar");

        $this->get("/recetas/compartida/{$receta->token}")->assertOk()->assertSee('Limonada')->assertSee('Azúcar');
        $this->get('/recetas/compartida/token-que-no-existe')->assertNotFound();
    }

    public function test_valida_la_receta(): void
    {
        $this->post('/recetas/store', ['titulo' => '', 'tipo_comida' => 'Otra', 'tiempo_min' => 0])
             ->assertSessionHasErrors(['titulo', 'tipo_comida', 'ingredientes', 'preparacion', 'tiempo_min', 'porciones']);
    }
}
