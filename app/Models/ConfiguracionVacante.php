<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfiguracionVacante extends Model
{
    //

    public function turno()
    {
        return $this->belongsTo('App\Models\Turno', 'turnos_id')->select("id", "denominacion");
    }
    public function area()
    {
        return $this->belongsTo('App\Models\Area', 'areas_id');
    }
    public function sede()
    {
        return $this->belongsTo('App\Models\Sede', 'sedes_id');
    }
}
