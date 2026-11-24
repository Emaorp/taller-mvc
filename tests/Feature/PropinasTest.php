<?php

namespace Tests\Feature;

use Tests\TestCase;

class PropinasTest extends TestCase
{
    public function test_calcula_la_propina_en_la_pantalla(): void
    {
        $this->post('/propinas/calcular', ['monto' => 100, 'porcentaje' => 10, 'personas' => 2])
             ->assertOk()
             ->assertSee('10,00')
             ->assertSee('110,00')
             ->assertSee('55,00');
    }

    public function test_propina_valida_los_datos(): void
    {
        $this->post('/propinas/calcular', ['monto' => -5, 'porcentaje' => 150])
             ->assertSessionHasErrors(['monto', 'porcentaje']);
    }
}
