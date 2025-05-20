<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tarifa extends Model
{
    public function conceptoPago()
    {
        return $this->belongsTo(ConceptoPago::class, 'concepto_pagos_id');
    }
    public function tipoColegio()
    {
        return $this->belongsTo(TipoColegio::class, 'tipo_colegios_id');
    }
}
