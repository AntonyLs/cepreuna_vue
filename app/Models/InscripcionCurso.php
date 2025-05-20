<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InscripcionCurso extends Model
{
    // public function curso(){
    //     return $this->belongsTo('App\Models\Curso','cursos_id');
    // }
    protected $fillable = [
        'inscripcion_docentes_id',  // Añadir aquí
        'curricula_detalles_id',
        // Agrega aquí otros campos que necesitas permitir para asignación masiva
    ];
    public function curso(){
        return $this->hasOneThrough(Curso::class,CurriculaDetalle::class,'id','id','curricula_detalles_id','cursos_id');
    }
}
