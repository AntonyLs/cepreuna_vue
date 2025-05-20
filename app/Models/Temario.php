<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Temario extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    public function periodo(){
        return $this->belongsTo(Periodo::class,'periodos_id');
    }
    public function curriculaDetalle(){
        return $this->belongsTo(CurriculaDetalle::class,'curricula_detalles_id');
    }
}
