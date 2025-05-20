<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCronogramaPagosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cronograma_pagos', function (Blueprint $table) {
            $table->id();
            $table->date('inicio');
            $table->date('fin');
            $table->tinyInteger('nro_cuota');
            $table->enum("estado",["0","1"])->comment("0:inactivo 1:activo");
            $table->text('observacion')->nullable();
            $table->unsignedBigInteger('periodos_id');
            $table->timestamps();

            $table->foreign('periodos_id')->references('id')->on('periodos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cronograma_pagos');
    }
}
