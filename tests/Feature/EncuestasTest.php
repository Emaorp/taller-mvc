<?php

namespace Tests\Feature;

use App\Models\Encuesta;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EncuestasTest extends TestCase
{
    use RefreshDatabase;

    private function crearEncuesta(): Encuesta
    {
        $this->post('/encuestas/store', [
            'titulo' => 'Satisfacción',
            'preguntas' => [
                ['texto' => '¿Te gustó el curso?', 'opciones' => ['Sí', 'No', '']],
                ['texto' => '¿Lo recomendarías?', 'opciones' => ['Sí', 'No']],
            ],
        ])->assertRedirect('/encuestas');

        return Encuesta::with('preguntas.opciones')->first();
    }

    private function responder(Encuesta $e, int $opcionP1, int $opcionP2)
    {
        return $this->post("/encuestas/responder/{$e->id}", ['respuestas' => [
            $e->preguntas[0]->id => $e->preguntas[0]->opciones[$opcionP1]->id,
            $e->preguntas[1]->id => $e->preguntas[1]->opciones[$opcionP2]->id,
        ]]);
    }

    public function test_crea_una_encuesta_con_preguntas_y_opciones(): void
    {
        $e = $this->crearEncuesta();

        $this->assertCount(2, $e->preguntas);
        $this->assertCount(2, $e->preguntas[0]->opciones);
    }

    public function test_exige_dos_opciones_por_pregunta(): void
    {
        $this->post('/encuestas/store', [
            'titulo' => 'Mala', 'preguntas' => [['texto' => 'Una sola', 'opciones' => ['A']]],
        ])->assertSessionHasErrors('preguntas.0.opciones');

        $this->assertDatabaseCount('encuestas', 0);
    }

    public function test_responde_y_calcula_resultados(): void
    {
        $e = $this->crearEncuesta();

        $this->responder($e, 0, 0);
        $this->responder($e, 0, 1);
        $this->responder($e, 1, 0);

        $informe = $e->resultados();

        $this->assertSame(3, $informe['participaciones']);
        $this->assertSame(2, $informe['preguntas'][0]['opciones'][0]['votos']);
        $this->assertSame(66.7, $informe['preguntas'][0]['opciones'][0]['porcentaje']);

        $this->get("/encuestas/resultados/{$e->id}")->assertOk()->assertSee('3 participaciones');
    }

    public function test_exige_responder_todas_las_preguntas(): void
    {
        $e = $this->crearEncuesta();

        $this->post("/encuestas/responder/{$e->id}", ['respuestas' => [$e->preguntas[0]->id => $e->preguntas[0]->opciones[0]->id]])
             ->assertSessionHasErrors();

        $this->assertDatabaseCount('participaciones', 0);
    }

    public function test_no_acepta_una_opcion_de_otra_pregunta(): void
    {
        $e = $this->crearEncuesta();

        $this->post("/encuestas/responder/{$e->id}", ['respuestas' => [
            $e->preguntas[0]->id => $e->preguntas[1]->opciones[0]->id,
            $e->preguntas[1]->id => $e->preguntas[1]->opciones[0]->id,
        ]])->assertSessionHasErrors();

        $this->assertDatabaseCount('respuestas', 0);
    }

    public function test_una_encuesta_cerrada_no_recibe_respuestas(): void
    {
        $e = $this->crearEncuesta();
        $this->patch("/encuestas/toggle/{$e->id}");

        $this->get("/encuestas/responder/{$e->id}")->assertRedirect('/encuestas');
        $this->responder($e, 0, 0);
        $this->assertDatabaseCount('participaciones', 0);
    }

    public function test_exporta_el_informe_en_csv_y_elimina(): void
    {
        $e = $this->crearEncuesta();
        $this->responder($e, 0, 0);

        $this->get("/encuestas/resultados/{$e->id}/csv")
             ->assertOk()
             ->assertHeader('content-type', 'text/csv; charset=UTF-8')
             ->assertDownload("encuesta-{$e->id}.csv");

        $this->delete("/encuestas/delete/{$e->id}");
        $this->assertDatabaseCount('encuestas', 0);
        $this->assertDatabaseCount('respuestas', 0);
    }
}
