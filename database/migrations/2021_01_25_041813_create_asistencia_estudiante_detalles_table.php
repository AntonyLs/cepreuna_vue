<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAsistenciaEstudianteDetallesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('asistencia_estudiante_detalles', function (Blueprint $table) {
            $table->id();
            $table->enum("estado",["1","2","3"])->comment("1:prensente 2:tarde 3:falta")->default("1");
            $table->unsignedBigInteger('asistencia_estudiantes_id');
            $table->unsignedBigInteger('estudiantes_id');
            $table->timestamps();

            $table->foreign('asistencia_estudiantes_id')->references('id')->on('asistencia_estudiantes');
            $table->foreign('estudiantes_id')->references('id')->on('estudiantes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('asistencia_estudiante_detalles');
    }
}
