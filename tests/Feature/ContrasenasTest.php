<?php

namespace Tests\Feature;

use Tests\TestCase;

class ContrasenasTest extends TestCase
{
    public function test_genera_contrasenas_con_la_longitud_pedida(): void
    {
        $respuesta = $this->post('/contrasenas/generar', [
            'longitud' => 20, 'cantidad' => 3, 'minusculas' => 1, 'numeros' => 1,
        ])->assertOk();

        preg_match_all('/class="form-control font-monospace" value="([^"]+)"/', $respuesta->getContent(), $m);

        $this->assertCount(3, $m[1]);
        foreach ($m[1] as $clave) {
            $this->assertSame(20, strlen(html_entity_decode($clave)));
            $this->assertMatchesRegularExpression('/^[a-z0-9]+$/', $clave);
        }
    }

    public function test_exige_al_menos_un_tipo_de_caracter(): void
    {
        $this->post('/contrasenas/generar', ['longitud' => 12, 'cantidad' => 1])->assertSessionHasErrors('tipos');
    }

    public function test_valida_la_longitud(): void
    {
        $this->post('/contrasenas/generar', ['longitud' => 2, 'cantidad' => 1, 'numeros' => 1])
             ->assertSessionHasErrors('longitud');
    }
}
