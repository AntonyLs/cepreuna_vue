<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Auxiliar extends Model
{
    protected $table = 'auxiliares';

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'users_id');
    }
}
