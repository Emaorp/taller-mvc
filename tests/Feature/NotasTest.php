<?php

namespace Tests\Feature;

use App\Models\CategoriaNota;
use App\Models\Nota;
use Database\Seeders\CategoriaNotaSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotasTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CategoriaNotaSeeder::class);
    }

    private function nota(string $titulo, string $contenido, string $categoria = 'Personal'): Nota
    {
        return Nota::create([
            'titulo' => $titulo, 'contenido' => $contenido,
            'categoria_nota_id' => CategoriaNota::where('nombre', $categoria)->first()->id,
        ]);
    }

    public function test_crea_edita_y_elimina_una_nota(): void
    {
        $categoria = CategoriaNota::first();

        $this->post('/notas/store', ['titulo' => 'Idea', 'contenido' => 'Algo', 'categoria_nota_id' => $categoria->id])
             ->assertRedirect('/notas');
        $nota = Nota::first();

        $this->put('/notas/update', [
            'nota_id' => $nota->id, 'titulo' => 'Idea 2', 'contenido' => 'Más', 'categoria_nota_id' => $categoria->id,
        ]);
        $this->assertSame('Idea 2', $nota->fresh()->titulo);

        $this->delete("/notas/delete/{$nota->id}");
        $this->assertDatabaseCount('notas', 0);
    }

    public function test_busca_por_titulo_contenido_y_categoria(): void
    {
        $this->nota('Lista de compras', 'leche y pan');
        $this->nota('Reunión', 'presentar el informe', 'Trabajo');

        $this->get('/notas?filter=compras')->assertSee('Lista de compras')->assertDontSee('Reunión');
        $this->get('/notas?filter=informe')->assertSee('Reunión')->assertDontSee('Lista de compras');

        $trabajo = CategoriaNota::where('nombre', 'Trabajo')->first();
        $this->get("/notas?categoria_id={$trabajo->id}")->assertSee('Reunión')->assertDontSee('Lista de compras');
    }

    public function test_crea_categorias_y_no_borra_las_que_tienen_notas(): void
    {
        $this->post('/notas/categorias/store', ['nombre' => 'Viajes']);
        $this->assertDatabaseHas('categorias_nota', ['nombre' => 'Viajes']);

        $this->post('/notas/categorias/store', ['nombre' => 'Viajes'])->assertSessionHasErrors('nombre');

        $personal = CategoriaNota::where('nombre', 'Personal')->first();
        $this->nota('Con categoría', 'x');
        $this->delete("/notas/categorias/delete/{$personal->id}");
        $this->assertDatabaseHas('categorias_nota', ['id' => $personal->id]);

        $viajes = CategoriaNota::where('nombre', 'Viajes')->first();
        $this->delete("/notas/categorias/delete/{$viajes->id}");
        $this->assertDatabaseMissing('categorias_nota', ['id' => $viajes->id]);
    }

    public function test_valida_la_nota(): void
    {
        $this->post('/notas/store', ['titulo' => '', 'contenido' => '', 'categoria_nota_id' => 99])
             ->assertSessionHasErrors(['titulo', 'contenido', 'categoria_nota_id']);
    }
}
