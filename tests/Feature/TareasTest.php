<?php

namespace Tests\Feature;

use App\Models\Tarea;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TareasTest extends TestCase
{
    use RefreshDatabase;

    public function test_agrega_una_tarea(): void
    {
        $this->post('/tareas/store', ['titulo' => 'Estudiar MVC'])->assertRedirect('/tareas');

        $this->assertDatabaseHas('tareas', ['titulo' => 'Estudiar MVC', 'completada' => false]);
        $this->get('/tareas')->assertSee('Estudiar MVC');
    }

    public function test_titulo_es_obligatorio(): void
    {
        $this->post('/tareas/store', ['titulo' => ''])->assertSessionHasErrors('titulo');
        $this->assertDatabaseCount('tareas', 0);
    }

    public function test_marca_como_completada_y_desmarca(): void
    {
        $tarea = Tarea::create(['titulo' => 'Hacer taller']);

        $this->patch("/tareas/toggle/{$tarea->id}");
        $this->assertTrue($tarea->fresh()->completada);

        $this->patch("/tareas/toggle/{$tarea->id}");
        $this->assertFalse($tarea->fresh()->completada);
    }

    public function test_elimina_una_tarea(): void
    {
        $tarea = Tarea::create(['titulo' => 'Borrar']);

        $this->delete("/tareas/delete/{$tarea->id}");

        $this->assertDatabaseMissing('tareas', ['id' => $tarea->id]);
    }

    public function test_filtra_por_estado(): void
    {
        Tarea::create(['titulo' => 'Pendiente uno']);
        Tarea::create(['titulo' => 'Hecha uno', 'completada' => true]);

        $this->get('/tareas?estado=pendientes')->assertSee('Pendiente uno')->assertDontSee('Hecha uno');
        $this->get('/tareas?estado=completadas')->assertSee('Hecha uno')->assertDontSee('Pendiente uno');
    }
}
