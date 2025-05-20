<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdjuntoCapacitaciones extends Model
{
    public function capacitacionTipo(){
        return $this->belongsTo('App\Models\CapacitacionTipo','capacitacion_tipos_id');
    }
}
