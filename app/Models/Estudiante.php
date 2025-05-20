<?php

namespace App\Models;

use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class Estudiante extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable, HasRoles;
    protected $guard_name = 'sanctum';


    public function tipoDocumento()
    {
        return $this->belongsTo(TipoDocumento::class, 'tipo_documentos_id');
    }
    public function colegio()
    {
        return $this->belongsTo(Colegio::class, 'colegios_id')->with('tipo_colegio');
    }
    public function pais()
    {
        return $this->belongsTo(Pais::class, 'pais_id');
    }
    public function ubigeo()
    {
        return $this->belongsTo(Ubigeo::class, 'ubigeos_id');
    }
    public function ubigeo_nacimiento()
    {
        return $this->belongsTo(Ubigeo::class, 'ubigeos_nacimiento', 'id');
    }
}
