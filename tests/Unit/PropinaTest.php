<?php

namespace Tests\Unit;

use App\Models\Propina;
use PHPUnit\Framework\TestCase;

class PropinaTest extends TestCase
{
    public function test_propina_del_15_por_ciento(): void
    {
        $r = Propina::calcular(200, 15);

        $this->assertSame(30.0, $r['propina']);
        $this->assertSame(230.0, $r['total']);
    }

    public function test_propina_dividida_entre_personas(): void
    {
        $r = Propina::calcular(100, 10, 4);

        $this->assertSame(10.0, $r['propina']);
        $this->assertSame(2.5, $r['propina_por_persona']);
        $this->assertSame(27.5, $r['total_por_persona']);
    }
}
