<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdjuntoCapacitacionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('adjunto_capacitaciones', function (Blueprint $table) {
            $table->id();
            $table->string("titulo",200);
            $table->text("path");
            $table->unsignedBigInteger('capacitacion_tipos_id');
            $table->unsignedBigInteger('inscripcion_docentes_id');
            $table->foreign('inscripcion_docentes_id')->references('id')->on('inscripcion_docentes');
            $table->foreign('capacitacion_tipos_id')->references('id')->on('capacitacion_tipos');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('adjunto_capacitaciones');
    }
}
