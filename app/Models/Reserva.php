<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Reserva extends Model
{
    protected $table = 'reservas';

    protected $guarded = [];

    protected $casts = [
        'fecha' => 'date',
    ];

    const PENDIENTE = 'Pendiente';
    const CONFIRMADA = 'Confirmada';
    const CANCELADA = 'Cancelada';

    const HORAS = ['08:00', '09:00', '10:00', '11:00', '12:00', '14:00', '15:00', '16:00'];

    public function servicio() {
        return $this->belongsTo(Servicio::class);
    }

    public static function horasOcupadas(int $servicioId, string $fecha): array {
        return self::where('servicio_id', $servicioId)
                   ->whereDate('fecha', $fecha)
                   ->where('estado', '!=', self::CANCELADA)
                   ->pluck('hora')
                   ->all();
    }

    public static function disponibilidad(int $servicioId, string $fecha): array {
        $ocupadas = self::horasOcupadas($servicioId, $fecha);
        $ahora = Carbon::now();
        $resultado = [];

        foreach (self::HORAS as $hora) {
            $pasada = Carbon::parse("$fecha $hora")->lt($ahora);
            $resultado[$hora] = !$pasada && !in_array($hora, $ocupadas);
        }

        return $resultado;
    }
}
