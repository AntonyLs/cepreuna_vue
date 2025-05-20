<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdjuntoProducciones extends Model
{
    public function produccion(){
        return $this->belongsTo('App\Models\Producciones','producciones_id');
    }
}
