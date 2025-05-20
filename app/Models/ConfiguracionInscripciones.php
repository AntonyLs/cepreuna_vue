<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfiguracionInscripciones extends Model
{
    public function periodo()
    {
        return $this->belongsTo('App\Models\Periodo', 'periodos_id');
    }
}
