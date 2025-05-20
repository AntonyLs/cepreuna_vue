<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMatriculaDetallesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('matricula_detalles', function (Blueprint $table) {
            $table->id();
            $table->enum("estado",["0","1","2"])->default("0")->comment("0:pendiente  1:vinculado  2:Error");
            $table->unsignedBigInteger('matriculas_id');
            $table->unsignedBigInteger('carga_academicas_id');
            $table->timestamps();
            $table->foreign('matriculas_id')->references('id')->on('matriculas');
            $table->foreign('carga_academicas_id')->references('id')->on('carga_academicas');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('matricula_detalles');
    }
}
