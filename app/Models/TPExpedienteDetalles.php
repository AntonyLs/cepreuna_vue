<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TPExpedienteDetalles extends Model
{
    protected $connection = 'mysql2'; // Conexión a la base de datos 'tramite_pagos'
    protected $table = 'expediente_detalles'; // Nombre de la tabla
    protected $fillable = ['hora_docente_id', 'id', 'expediente_id']; // Asegúrate de agregar otros campos según tu tabla
}
