<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Permission\Traits\HasRoles;
class DocenteApto extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable,HasRoles;
    protected $guard_name = 'sanctum';
    public function docente(){
        return $this->belongsTo('App\Models\Docente','docentes_id');
    }
}
