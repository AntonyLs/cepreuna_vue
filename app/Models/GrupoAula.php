<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class GrupoAula extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    public function aula()
    {
        return $this->belongsTo('App\Models\Aula', 'aulas_id')->with("local");
    }
    public function grupo()
    {
        return $this->belongsTo('App\Models\Grupo', 'grupos_id')->select("id", "denominacion");
    }
    public function turno()
    {
        return $this->belongsTo('App\Models\Turno', 'turnos_id')->select("id", "denominacion");
    }
    public function periodo()
    {
        return $this->belongsTo('App\Models\Periodo', 'periodos_id');
    }
    public function area()
    {
        return $this->belongsTo('App\Models\Area', 'areas_id');
    }
}
