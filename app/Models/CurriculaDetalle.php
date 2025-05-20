<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CurriculaDetalle extends Model
{
    public function curso(){
        return $this->belongsTo('App\Models\Curso','cursos_id');
    }
    public function curricula(){
        return $this->belongsTo('App\Models\Curricula','curriculas_id');
    }
}
