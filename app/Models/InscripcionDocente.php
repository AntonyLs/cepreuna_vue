<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class InscripcionDocente extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    public function docente(){
        return $this->belongsTo('App\Models\Docente','docentes_id')->with("DocenteApto");
    }
    public function area(){
        return $this->belongsTo('App\Models\Area','areas_id');
    }
    public function programa(){
        return $this->hasOneThrough(Programa::class,Docente::class,'id','id','docentes_id','programas_id');
    }
    public function grado(){
        return $this->hasOneThrough(GradoAcademico::class,Docente::class,'id','id','docentes_id','grado_academicos_id');
    }
    public function disponibilidades(){
        return $this->hasMany(Disponibilidades::class,'inscripcion_docentes_id','id')->with("sede");
    }
    public function grados(){
        return $this->hasMany(AdjuntoGrado::class,'inscripcion_docentes_id','id')->with(["gradoAcademico","programa"]);
    }
    public function experiencias(){
        return $this->hasMany(AdjuntoExperiencia::class,'inscripcion_docentes_id','id')->with("experiencia");
    }
}
