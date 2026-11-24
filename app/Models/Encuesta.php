<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Encuesta extends Model
{
    protected $table = 'encuestas';

    protected $guarded = [];

    protected $casts = [
        'abierta' => 'boolean',
    ];

    public function preguntas() {
        return $this->hasMany(Pregunta::class)->orderBy('orden');
    }

    public function participaciones() {
        return $this->hasMany(Participacion::class);
    }

    public function resultados(): array {
        $totalParticipaciones = $this->participaciones()->count();

        $this->load('preguntas.opciones');

        $informe = [];

        foreach ($this->preguntas as $pregunta) {
            $votosPorOpcion = Respuesta::where('pregunta_id', $pregunta->id)
                                       ->selectRaw('opcion_id, COUNT(*) as votos')
                                       ->groupBy('opcion_id')
                                       ->pluck('votos', 'opcion_id');

            $totalPregunta = (int) $votosPorOpcion->sum();

            $opciones = $pregunta->opciones->map(function ($opcion) use ($votosPorOpcion, $totalPregunta) {
                $votos = (int) ($votosPorOpcion[$opcion->id] ?? 0);

                return [
                    'texto' => $opcion->texto,
                    'votos' => $votos,
                    'porcentaje' => $totalPregunta > 0 ? round($votos * 100 / $totalPregunta, 1) : 0,
                ];
            })->values()->all();

            $informe[] = [
                'pregunta' => $pregunta->texto,
                'total' => $totalPregunta,
                'opciones' => $opciones,
            ];
        }

        return [ 'participaciones' => $totalParticipaciones, 'preguntas' => $informe ];
    }
}
