<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHorarioInscripcionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('horario_inscripciones', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('areas_id');
            $table->unsignedBigInteger('curricula_detalles_id');
            $table->unsignedBigInteger('plantilla_horarios_id');
            $table->enum("dia",["1","2","3","4","5"])->comment("1:lunes 2:martes ... 5:viernes");
            $table->timestamps();
            $table->foreign('areas_id')->references('id')->on('areas');
            $table->foreign('curricula_detalles_id')->references('id')->on('curricula_detalles');
            $table->foreign('plantilla_horarios_id')->references('id')->on('plantilla_horarios');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('horario_inscripciones');
    }
}
