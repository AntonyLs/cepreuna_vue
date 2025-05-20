<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CalificacionDocente extends Model
{
    public function docente(){
        return $this->belongsTo('App\Models\Docente','docentes_id');
    }
    public function asistenciaDocente(){
        return $this->belongsTo('App\Models\AsistenciaDocente','asistencia_docentes_id');
    }
    public function curso()
    {
        // una inscripcion tiene un pago a traves de
        return $this->hasOneThrough(Curso::class,CargaAcademica::class,'id','id','carga_academicas_id','cursos_id');
    }
}
