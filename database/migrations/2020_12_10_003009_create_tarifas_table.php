<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTarifasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tarifas', function (Blueprint $table) {
            $table->id();
            $table->string("denominacion",50);
            $table->float("importe");
            $table->enum("estado",["0","1"])->commet("0:inactivo  1:activo");
            $table->enum("tipo_estudiante",["1","2"])->commet("1:normal  2:hijo de trabajador");
            $table->unsignedBigInteger('tipo_colegios_id');
            $table->unsignedBigInteger('concepto_pagos_id');
            $table->timestamps();

            $table->foreign('tipo_colegios_id')->references('id')->on('tipo_colegios');
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
        Schema::dropIfExists('tarifas');
    }
}
