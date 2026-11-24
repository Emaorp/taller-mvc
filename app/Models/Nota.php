<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nota extends Model
{
    protected $table = 'notas';

    protected $guarded = [];

    public function categoria() {
        return $this->belongsTo(CategoriaNota::class, 'categoria_nota_id');
    }

    public function scopeBuscar($consulta, ?string $texto) {
        if ($texto === null || trim($texto) === '') {
            return $consulta;
        }

        return $consulta->where(function ($q) use ($texto) {
            $q->where('titulo', 'LIKE', "%$texto%")->orWhere('contenido', 'LIKE', "%$texto%");
        });
    }
}
