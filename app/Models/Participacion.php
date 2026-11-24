<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Participacion extends Model
{
    protected $table = 'participaciones';

    protected $guarded = [];

    public function respuestas() {
        return $this->hasMany(Respuesta::class);
    }
}
