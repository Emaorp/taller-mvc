<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Servicio extends Model
{
    protected $table = 'servicios';

    protected $guarded = [];

    public function reservas() {
        return $this->hasMany(Reserva::class);
    }
}
