<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SesionCronometro extends Model
{
    protected $table = 'sesiones_cronometro';

    protected $guarded = [];

    protected $casts = [
        'vueltas' => 'array',
    ];

    public static function formatear(int $ms): string {
        $centesimas = intdiv($ms % 1000, 10);
        $totalSegundos = intdiv($ms, 1000);
        $horas = intdiv($totalSegundos, 3600);
        $minutos = intdiv($totalSegundos % 3600, 60);
        $segundos = $totalSegundos % 60;

        $texto = sprintf('%02d:%02d.%02d', $minutos, $segundos, $centesimas);

        return $horas > 0 ? sprintf('%02d:', $horas) . $texto : $texto;
    }
}
