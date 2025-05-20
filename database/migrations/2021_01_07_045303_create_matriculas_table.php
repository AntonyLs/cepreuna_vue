<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMatriculasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('matriculas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('estudiantes_id');
            $table->unsignedBigInteger('curriculas_id');
            $table->unsignedBigInteger('grupo_aulas_id');
            $table->unsignedBigInteger('periodos_id');
            $table->unsignedBigInteger('turnos_id');
            $table->timestamps();

            $table->foreign('estudiantes_id')->references('id')->on('estudiantes');
            $table->foreign('curriculas_id')->references('id')->on('curriculas');
            $table->foreign('grupo_aulas_id')->references('id')->on('grupo_aulas');
            $table->foreign('periodos_id')->references('id')->on('periodos');
            $table->foreign('turnos_id')->references('id')->on('turnos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('matriculas');
    }
}
