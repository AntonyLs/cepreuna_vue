<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdjuntoGrado extends Model
{
    public function gradoAcademico()
    {
        return $this->belongsTo('App\Models\GradoAcademico', 'grado_academicos_id');
    }
    public function programa()
    {
        return $this->belongsTo('App\Models\Programa', 'programas_id');
    }
}
