<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Sesiones extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    public function carga(){
        return $this->belongsTo('App\Models\CargaAcademica','carga_academicas_id');
    }
}
