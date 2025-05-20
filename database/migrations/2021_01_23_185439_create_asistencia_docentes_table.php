<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAsistenciaDocentesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('asistencia_docentes', function (Blueprint $table) {
            $table->id();
            $table->enum("estado",["1","2","3"])->comment("1:prensente 2:tarde 3:falta")->default("1");
            $table->date("fecha");
            $table->time("hora_inicio");
            $table->time("hora_fin");
            $table->smallInteger("cantidad_horas");
            $table->text("observacion")->nullable();
            $table->unsignedBigInteger('docentes_id');
            $table->unsignedBigInteger('carga_academicas_id');
            $table->unsignedBigInteger('sesiones_id')->nullable();
            $table->unsignedBigInteger('users_id');
            $table->timestamps();

            $table->foreign('docentes_id')->references('id')->on('docentes');
            $table->foreign('carga_academicas_id')->references('id')->on('carga_academicas');
            $table->foreign('sesiones_id')->references('id')->on('sesiones');
            $table->foreign('users_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('asistencia_docentes');
    }
}
