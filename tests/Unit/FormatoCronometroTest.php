<?php

namespace Tests\Unit;

use App\Models\SesionCronometro;
use PHPUnit\Framework\TestCase;

class FormatoCronometroTest extends TestCase
{
    public function test_formato_de_tiempo_del_cronometro(): void
    {
        $this->assertSame('01:05.25', SesionCronometro::formatear(65250));
        $this->assertSame('01:00:00.00', SesionCronometro::formatear(3600000));
    }
}
