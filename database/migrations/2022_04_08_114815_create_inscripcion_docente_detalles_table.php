<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInscripcionDocenteDetallesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inscripcion_docente_detalles', function (Blueprint $table) {
            $table->id();
            $table->smallInteger('puntaje');
            $table->unsignedBigInteger('criterios_id');
            $table->unsignedBigInteger('inscripcion_docentes_id');
            $table->timestamps();
            $table->foreign('criterios_id')->references('id')->on('criterios');
            $table->foreign('inscripcion_docentes_id')->references('id')->on('inscripcion_docentes');

            $table->unique(['criterios_id', 'inscripcion_docentes_id'], 'unique_criterio_inscripcion_docente_calificacion');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inscripcion_docente_detalles');
    }
}
