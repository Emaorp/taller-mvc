<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pregunta extends Model
{
    protected $table = 'preguntas';

    protected $guarded = [];

    public function encuesta() {
        return $this->belongsTo(Encuesta::class);
    }

    public function opciones() {
        return $this->hasMany(Opcion::class);
    }
}
