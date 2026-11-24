<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Receta extends Model
{
    protected $table = 'recetas';

    protected $guarded = [];

    protected $casts = [
        'guardada' => 'boolean',
    ];

    const TIPOS = ['Desayuno', 'Almuerzo', 'Cena', 'Postre', 'Snack', 'Bebida'];

    protected static function booted(): void {
        static::creating(function (Receta $receta) {
            if (empty($receta->token)) {
                $receta->token = (string) Str::uuid();
            }
        });
    }

    public function listaIngredientes(): array {
        return array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $this->ingredientes))));
    }

    public function scopeBuscar($consulta, ?string $texto) {
        if ($texto === null || trim($texto) === '') {
            return $consulta;
        }

        return $consulta->where(function ($q) use ($texto) {
            $q->where('titulo', 'LIKE', "%$texto%")->orWhere('ingredientes', 'LIKE', "%$texto%");
        });
    }
}
