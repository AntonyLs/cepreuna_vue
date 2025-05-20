<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Horario extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    public function carga(){
        return $this->belongsTo('App\Models\CargaAcademica','carga_academicas_id')->with("docente");
    }
    public function curso()
    {
        // una inscripcion tiene un pago a traves de
        return $this->hasOneThrough(Curso::class,CargaAcademica::class,'id','id','carga_academicas_id','cursos_id');
    }
}
