<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Evento extends Model
{
    protected $table = 'eventos';

    protected $guarded = [];

    protected $casts = [
        'fecha' => 'date',
    ];

    const RECORDATORIOS = [
        5 => '5 minutos antes',
        10 => '10 minutos antes',
        15 => '15 minutos antes',
        30 => '30 minutos antes',
        60 => '1 hora antes',
        120 => '2 horas antes',
        1440 => '1 día antes',
    ];

    public function inicio(): Carbon {
        return Carbon::parse($this->fecha->toDateString() . ' ' . ($this->hora ?: '00:00'));
    }

    public static function calendario(Carbon $inicioMes): array {
        $desde = $inicioMes->copy()->startOfWeek(Carbon::MONDAY);
        $hasta = $inicioMes->copy()->endOfMonth()->endOfWeek(Carbon::SUNDAY);

        $eventos = self::whereBetween('fecha', [$desde->toDateString(), $hasta->toDateString()])
                       ->orderBy('hora')
                       ->get()
                       ->groupBy(fn ($e) => $e->fecha->toDateString());

        $semanas = [];
        $dia = $desde->copy();

        while ($dia->lte($hasta)) {
            $semana = [];

            for ($i = 0; $i < 7; $i++) {
                $semana[] = [
                    'fecha' => $dia->copy(),
                    'en_mes' => $dia->month == $inicioMes->month,
                    'es_hoy' => $dia->isToday(),
                    'eventos' => $eventos->get($dia->toDateString(), collect()),
                ];
                $dia->addDay();
            }

            $semanas[] = $semana;
        }

        return $semanas;
    }

    public static function conRecordatorioProximo(): array {
        $ahora = Carbon::now();

        return self::whereNotNull('recordatorio_min')
            ->whereNotNull('hora')
            ->whereBetween('fecha', [$ahora->copy()->subDay()->toDateString(), $ahora->copy()->addDays(2)->toDateString()])
            ->get()
            ->filter(fn ($e) => $e->inicio()->gte($ahora->copy()->subMinutes(10)))
            ->map(fn ($e) => [
                'id' => $e->id,
                'titulo' => $e->titulo,
                'inicio' => $e->inicio()->toIso8601String(),
                'recordatorio_min' => $e->recordatorio_min,
            ])
            ->values()
            ->all();
    }
}
