<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TPExpediente extends Model
{
    protected $connection = "mysql2";

    protected $table = "expedientes";
    public function docente()
    {
        return $this->belongsTo('App\Models\TPDocente', 'docente_id');
    }
}
