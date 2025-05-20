<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TPDocente extends Model
{
    protected $connection = "mysql2";

    protected $table = "docentes";

    public function periodo()
    {
        return $this->belongsTo('App\Models\TPPeriodo', 'periodo_id');
    }
    public function expediente()
    {
        return $this->hasMany('App\Models\TPExpediente', 'docente_id');
    }
    public function horasDocente()
    {
        return $this->hasMany(TPHorasDocente::class, 'docente_id');
    }
}
