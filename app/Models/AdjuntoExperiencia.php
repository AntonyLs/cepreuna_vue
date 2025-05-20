<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdjuntoExperiencia extends Model
{
    public function experiencia(){
        return $this->belongsTo('App\Models\Experiencia','experiencias_id');
    }
}
