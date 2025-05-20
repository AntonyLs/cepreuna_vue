<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInscripcionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inscripciones', function (Blueprint $table) {
            $table->id();
            $table->string('correlativo',5);
            $table->enum("tipo_estudiante",["1","2","3","4","5"])->comment("1:normal  2:hijo de trabajador 3:descuento trabajador UNA 4: descuento hermano 5: inscripcion por RR");
            $table->enum("estado",["0","1"])->comment("0:pre inscrito  1:inscrito");
            $table->smallInteger('cantidad_inscrito');
            $table->unsignedBigInteger('estudiantes_id');
            $table->unsignedBigInteger('areas_id');
            $table->unsignedBigInteger('sedes_id');
            $table->unsignedBigInteger('periodos_id');
            $table->unsignedBigInteger('turnos_id');
            $table->timestamps();

            $table->foreign('estudiantes_id')->references('id')->on('estudiantes');
            $table->foreign('areas_id')->references('id')->on('areas');
            $table->foreign('sedes_id')->references('id')->on('sedes');
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
        Schema::dropIfExists('inscripciones');
    }
}
