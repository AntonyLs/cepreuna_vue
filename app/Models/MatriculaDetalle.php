<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MatriculaDetalle extends Model
{
    public function cargaAcademica(){
        return $this->belongsTo('App\Models\CargaAcademica','carga_academicas_id');
    }
    public function curso()
    {
        // una inscripcion tiene un pago a traves de
        return $this->hasOneThrough(Curso::class,CargaAcademica::class,'id','id','carga_academicas_id','cursos_id');
    }
}
