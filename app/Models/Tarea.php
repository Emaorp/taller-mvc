<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tarea extends Model
{
    protected $table = 'tareas';

    protected $guarded = [];

    protected $casts = [
        'completada' => 'boolean',
    ];
}
