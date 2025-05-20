<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class AsistenciaDocente extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    public function docente(){
        return $this->belongsTo(Docente::class,'docentes_id')->orderBy("paterno","asc");
    }
    public function docenteApto(){
        return $this->belongsTo(DocenteApto::class,'docentes_id','docentes_id');
    }
    public function carga(){
        return $this->belongsTo(CargaAcademica::class,'carga_academicas_id')->with(['grupo','area','curso','grupoAula']);
    }
    public function curso()
    {
        // una inscripcion tiene un pago a traves de
        return $this->hasOneThrough(Curso::class,CargaAcademica::class,'id','id','carga_academicas_id','cursos_id');
    }
    public function sesiones(){
        return $this->belongsTo('App\Models\Sesiones','sesiones_id');
    }
    public function user(){
        return $this->belongsTo(User::class,'users_id');
    }
}
