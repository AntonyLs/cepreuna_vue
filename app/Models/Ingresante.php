<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ingresante extends Model 
{
    protected $filleable = ['dni','nombres','puntaje','carrera','fecha_examen'];
    protected $guarded = [];
}
