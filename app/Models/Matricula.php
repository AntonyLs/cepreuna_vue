<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Matricula extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    public function estudiante(){
        return $this->belongsTo('App\Models\Estudiante','estudiantes_id');
    }
    public function turno(){
        return $this->belongsTo('App\Models\Turno','turnos_id');
    }
    public function grupoAula(){
        return $this->belongsTo('App\Models\GrupoAula','grupo_aulas_id')->with(["grupo","turno","aula","area"]);
    }
}
