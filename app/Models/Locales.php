<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Locales extends Model
{
    public function sede(){
        return $this->belongsTo('App\Models\Sede','sedes_id');
    }
}
