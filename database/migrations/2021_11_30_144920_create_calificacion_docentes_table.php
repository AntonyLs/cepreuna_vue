<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCalificacionDocentesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('calificacion_docentes', function (Blueprint $table) {
            $table->id();
            $table->smallInteger('participantes')->default("0");
            $table->decimal('promedio')->default("0");
            $table->enum('estado', ["0", "1"])->comment("0:pediente  1:calificado")->default("1");
            $table->unsignedBigInteger('docentes_id');
            $table->unsignedBigInteger('carga_academicas_id');
            $table->unsignedBigInteger('asistencia_docentes_id');
            $table->timestamps();

            $table->foreign('docentes_id')->references('id')->on('docentes');
            $table->foreign('carga_academicas_id')->references('id')->on('carga_academicas');
            $table->foreign('asistencia_docentes_id')->references('id')->on('asistencia_docentes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('calificacion_docentes');
    }
}
