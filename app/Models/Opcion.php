<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Opcion extends Model
{
    protected $table = 'opciones';

    protected $guarded = [];

    public function pregunta() {
        return $this->belongsTo(Pregunta::class);
    }
}
