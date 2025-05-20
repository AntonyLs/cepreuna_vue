<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdjuntoGradosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('adjunto_grados', function (Blueprint $table) {
            $table->id();
            // $table->string("titulo",200);
            $table->text("path");
            $table->unsignedBigInteger('grado_academicos_id');
            $table->unsignedBigInteger('programas_id');
            $table->unsignedBigInteger('inscripcion_docentes_id');
            $table->timestamps();
            $table->foreign('grado_academicos_id')->references('id')->on('grado_academicos');
            $table->foreign('programas_id')->references('id')->on('programas');
            $table->foreign('inscripcion_docentes_id')->references('id')->on('inscripcion_docentes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('adjunto_grados');
    }
}
