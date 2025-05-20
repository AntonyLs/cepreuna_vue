<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCargaAcademicasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('carga_academicas', function (Blueprint $table) {
            $table->id();
            $table->string("idclassroom",100)->nullable();
            $table->enum("tipo",["1","2"])->comment("1: normal  2: Suplente");
            $table->unsignedBigInteger('docentes_id')->nullable();
            $table->unsignedBigInteger('cursos_id');
            $table->unsignedBigInteger('grupo_aulas_id');
            $table->unsignedBigInteger('periodos_id');
            $table->timestamps();

            $table->foreign('docentes_id')->references('id')->on('docentes');
            $table->foreign('cursos_id')->references('id')->on('cursos');
            $table->foreign('grupo_aulas_id')->references('id')->on('grupo_aulas');
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
        Schema::dropIfExists('carga_academicas');
    }
}
