<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInscripcionPagosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inscripcion_pagos', function (Blueprint $table) {
            $table->id();
            $table->decimal("monto",10,2);
            $table->unsignedBigInteger('inscripciones_id');
            $table->unsignedBigInteger('pagos_id');
            $table->unsignedBigInteger('concepto_pagos_id');
            $table->timestamps();

            $table->foreign('inscripciones_id')->references('id')->on('inscripciones');
            $table->foreign('pagos_id')->references('id')->on('pagos');
            $table->foreign('concepto_pagos_id')->references('id')->on('concepto_pagos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inscripcion_pagos');
    }
}
