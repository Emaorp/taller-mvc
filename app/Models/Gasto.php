<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Gasto extends Model
{
    protected $table = 'gastos';

    protected $guarded = [];

    protected $casts = [
        'fecha' => 'date',
        'monto' => 'decimal:2',
    ];

    public function categoria() {
        return $this->belongsTo(CategoriaGasto::class, 'categoria_gasto_id');
    }

    public static function inicioDeMes(?string $mes): Carbon {
        if ($mes && preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $mes)) {
            return Carbon::createFromFormat('Y-m-d', $mes . '-01')->startOfDay();
        }

        return Carbon::now()->startOfMonth();
    }

    public static function delMes(Carbon $inicio) {
        return self::whereBetween('fecha', [$inicio->toDateString(), $inicio->copy()->endOfMonth()->toDateString()]);
    }

    public static function resumenMensual(Carbon $inicio): array {
        $gastos = self::delMes($inicio)->with('categoria')->get();

        $total = (float) $gastos->sum('monto');

        $porCategoria = $gastos->groupBy('categoria_gasto_id')->map(function ($grupo) use ($total) {
            $suma = (float) $grupo->sum('monto');

            return [
                'categoria' => $grupo->first()->categoria->nombre,
                'monto' => $suma,
                'cantidad' => $grupo->count(),
                'porcentaje' => $total > 0 ? round($suma * 100 / $total, 1) : 0,
            ];
        })->sortByDesc('monto')->values();

        $diasTranscurridos = $inicio->isSameMonth(Carbon::now()) ? Carbon::now()->day : $inicio->daysInMonth;

        return [
            'total' => $total,
            'cantidad' => $gastos->count(),
            'promedio_diario' => $diasTranscurridos > 0 ? round($total / $diasTranscurridos, 2) : 0,
            'por_categoria' => $porCategoria,
        ];
    }

    public static function totalesUltimosMeses(Carbon $fin, int $meses = 6): array {
        $resultado = [];

        for ($i = $meses - 1; $i >= 0; $i--) {
            $inicio = $fin->copy()->subMonthsNoOverflow($i)->startOfMonth();
            $resultado[] = [
                'mes' => $inicio->format('Y-m'),
                'etiqueta' => $inicio->translatedFormat('M Y'),
                'total' => (float) self::delMes($inicio)->sum('monto'),
            ];
        }

        return $resultado;
    }
}
