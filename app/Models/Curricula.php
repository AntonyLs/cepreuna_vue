<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Curricula extends Model
{
    public function area(){
        return $this->belongsTo('App\Models\Area','areas_id');
    }
}
