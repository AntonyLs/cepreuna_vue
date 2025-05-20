<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlantillaHorario extends Model
{
    public function turno()
    {
        return $this->belongsTo('App\Models\Turno', 'turnos_id')->select("id", "denominacion");
    }
}
