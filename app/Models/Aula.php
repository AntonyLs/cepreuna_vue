<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Aula extends Model
{
    public function local()
    {
        return $this->belongsTo('App\Models\Locales', 'locales_id')->with("sede");
    }
}
