<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class AsistenciaEstudiante extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    public function grupo()
    {
        // una inscripcion tiene un pago a traves de
        return $this->hasOneThrough(Grupo::class, GrupoAula::class, 'id', 'id', 'grupo_aulas_id', 'grupos_id');
    }
}
