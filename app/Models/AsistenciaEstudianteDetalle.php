<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class AsistenciaEstudianteDetalle extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    public function estudiante()
    {
        return $this->belongsTo('App\Models\Estudiante', 'estudiantes_id');
    }
}
