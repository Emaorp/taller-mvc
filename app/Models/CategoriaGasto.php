<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoriaGasto extends Model
{
    protected $table = 'categorias_gasto';

    protected $guarded = [];

    public function gastos() {
        return $this->hasMany(Gasto::class, 'categoria_gasto_id');
    }
}
