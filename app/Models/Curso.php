<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Curso extends Model
{
    //
    public function area()
    {
        return $this->belongsTo(Area::class, 'areas_id');
    }
}
