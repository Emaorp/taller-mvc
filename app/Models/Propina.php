<?php

namespace App\Models;

class Propina
{
    public static function calcular(float $monto, float $porcentaje, int $personas = 1): array {
        $personas = max(1, $personas);

        $propina = round($monto * $porcentaje / 100, 2);
        $total = round($monto + $propina, 2);

        return [
            'monto' => $monto,
            'porcentaje' => $porcentaje,
            'personas' => $personas,
            'propina' => $propina,
            'total' => $total,
            'propina_por_persona' => round($propina / $personas, 2),
            'total_por_persona' => round($total / $personas, 2),
        ];
    }
}
