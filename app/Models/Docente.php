<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Docente extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    public function tipoDocumento()
    {
        return $this->belongsTo('App\Models\TipoDocumento', 'tipo_documentos_id');
    }
    public function gradoAcademico()
    {
        return $this->belongsTo('App\Models\GradoAcademico', 'grado_academicos_id');
    }
    public function programa()
    {
        return $this->belongsTo('App\Models\Programa', 'programas_id');
    }
    public function DocenteApto()
    {
        return $this->hasOne(DocenteApto::class, 'docentes_id','id');
    }
    public function ubigeo()
    {
        return $this->belongsTo('App\Models\Ubigeo', 'ubigeos_id');
    }
}
