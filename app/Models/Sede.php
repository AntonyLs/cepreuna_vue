<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sede extends Model
{
    public function ubigeo(){
        return $this->belongsTo('App\Models\Ubigeo','ubigeos_id');
    }
}
