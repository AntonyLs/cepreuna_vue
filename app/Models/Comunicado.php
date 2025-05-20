<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comunicado extends Model
{
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'users_id');
    }
}
