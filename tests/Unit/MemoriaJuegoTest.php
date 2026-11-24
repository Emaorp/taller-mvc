<?php

namespace Tests\Unit;

use App\Models\Memoria;
use PHPUnit\Framework\TestCase;

class MemoriaJuegoTest extends TestCase
{
    public function test_baraja_tiene_cada_imagen_dos_veces(): void
    {
        foreach (Memoria::NIVELES as $clave => $nivel) {
            $baraja = Memoria::generarBaraja($clave);
            $conteo = array_count_values(array_column($baraja, 'par'));

            $this->assertCount($nivel['pares'] * 2, $baraja);
            $this->assertCount($nivel['pares'], $conteo);
            $this->assertSame([2], array_values(array_unique($conteo)));
        }
    }

    public function test_puntaje_de_memoria(): void
    {
        $this->assertSame(500, Memoria::calcularPuntaje('facil', 10, 30));
        $this->assertSame(0, Memoria::calcularPuntaje('facil', 100, 600));
    }
}
