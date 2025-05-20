<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TPHorasDocente extends Model
{
    protected $connection = "mysql2";

    protected $table = "horas_docente";
    protected $fillable = ['cantidad', 'id', 'docente_id', 'mes_id', 'tipo_pago_id'];
}
