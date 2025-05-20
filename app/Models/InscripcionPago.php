<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class InscripcionPago extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    public function conceptoPago()
    {
        return $this->belongsTo(ConceptoPago::class, 'concepto_pagos_id');
    }
}
