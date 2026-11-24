<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoriaNota extends Model
{
    protected $table = 'categorias_nota';

    protected $guarded = [];

    public function notas() {
        return $this->hasMany(Nota::class, 'categoria_nota_id');
    }
}
