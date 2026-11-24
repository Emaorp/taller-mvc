<?php

namespace Tests\Unit;

use App\Models\GeneradorContrasena;
use PHPUnit\Framework\TestCase;

class GeneradorContrasenaTest extends TestCase
{
    public function test_contrasena_tiene_la_longitud_y_un_caracter_de_cada_tipo(): void
    {
        $conjuntos = GeneradorContrasena::conjuntos(true, true, true, true, false);

        for ($i = 0; $i < 50; $i++) {
            $clave = GeneradorContrasena::generar(12, $conjuntos);

            $this->assertSame(12, strlen($clave));
            $this->assertMatchesRegularExpression('/[a-z]/', $clave);
            $this->assertMatchesRegularExpression('/[A-Z]/', $clave);
            $this->assertMatchesRegularExpression('/[0-9]/', $clave);
            $this->assertMatchesRegularExpression('/[^a-zA-Z0-9]/', $clave);
        }
    }

    public function test_contrasena_evita_caracteres_ambiguos(): void
    {
        $conjuntos = GeneradorContrasena::conjuntos(true, true, true, false, true);

        for ($i = 0; $i < 50; $i++) {
            $this->assertDoesNotMatchRegularExpression('/[il1Lo0O]/', GeneradorContrasena::generar(20, $conjuntos));
        }
    }
}
