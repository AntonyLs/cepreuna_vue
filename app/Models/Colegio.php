<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Colegio extends Model
{
    //

    public function tipo_colegio()
    {
        return $this->belongsTo(TipoColegio::class, 'tipo_colegios_id');
    }
}
