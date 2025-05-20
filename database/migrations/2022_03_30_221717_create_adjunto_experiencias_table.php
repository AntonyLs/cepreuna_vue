<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdjuntoExperienciasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('adjunto_experiencias', function (Blueprint $table) {
            $table->id();
            $table->text("path");
            $table->unsignedBigInteger('experiencias_id');
            $table->unsignedBigInteger('inscripcion_docentes_id');
            $table->timestamps();
            $table->foreign('inscripcion_docentes_id')->references('id')->on('inscripcion_docentes');
            $table->foreign('experiencias_id')->references('id')->on('experiencias');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('adjunto_experiencias');
    }
}
