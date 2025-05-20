<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CalificacionDocenteDetalle extends Model
{
    public function criterio(){
        return $this->belongsTo('App\Models\Criterio','criterios_id');
    }
}
