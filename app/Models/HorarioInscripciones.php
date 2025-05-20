<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HorarioInscripciones extends Model
{
    public function curso()
    {
        // una inscripcion tiene un pago a traves de
        return $this->hasOneThrough(Curso::class,CurriculaDetalle::class,'id','id','curricula_detalles_id','cursos_id');
    }
}
