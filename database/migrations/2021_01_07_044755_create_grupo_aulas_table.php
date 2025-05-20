<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGrupoAulasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('grupo_aulas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('grupos_id');
            $table->unsignedBigInteger('aulas_id');
            $table->unsignedBigInteger('areas_id');
            $table->unsignedBigInteger('turnos_id');
            $table->unsignedBigInteger('periodos_id');
            $table->timestamps();
            
            $table->foreign('grupos_id')->references('id')->on('grupos');
            $table->foreign('aulas_id')->references('id')->on('aulas');
            $table->foreign('areas_id')->references('id')->on('areas');
            $table->foreign('turnos_id')->references('id')->on('turnos');
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
        Schema::dropIfExists('grupo_aulas');
    }
}
