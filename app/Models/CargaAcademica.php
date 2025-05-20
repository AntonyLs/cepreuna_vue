<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class CargaAcademica extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    public function curso(){
        return $this->belongsTo('App\Models\Curso','cursos_id');
    }
    public function docente(){
        return $this->belongsTo('App\Models\Docente','docentes_id');
    }
    public function grupoAula(){
        return $this->belongsTo('App\Models\GrupoAula','grupo_aulas_id')->with(["aula","area","grupo"]);
    }
    public function grupo()
    {
        // una inscripcion tiene un pago a traves de
        return $this->hasOneThrough(Grupo::class,GrupoAula::class,'id','id','grupo_aulas_id','grupos_id');
    }
    public function aula()
    {
        // una inscripcion tiene un pago a traves de
        return $this->hasOneThrough(Aula::class,GrupoAula::class,'id','id','grupo_aulas_id','aulas_id');
    }
    public function area()
    {
        // una inscripcion tiene un pago a traves de
        return $this->hasOneThrough(Area::class,GrupoAula::class,'id','id','grupo_aulas_id','areas_id');
    }
}
