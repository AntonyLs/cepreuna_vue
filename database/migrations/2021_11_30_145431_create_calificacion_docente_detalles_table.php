<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCalificacionDocenteDetallesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('calificacion_docente_detalles', function (Blueprint $table) {
            $table->id();
            $table->smallInteger('puntaje');
            $table->unsignedBigInteger('criterios_id');
            $table->unsignedBigInteger('estudiantes_id');
            $table->unsignedBigInteger('calificacion_docentes_id');
            $table->timestamps();

            $table->foreign('criterios_id')->references('id')->on('criterios');
            $table->foreign('estudiantes_id')->references('id')->on('estudiantes');
            $table->foreign('calificacion_docentes_id')->references('id')->on('calificacion_docentes');

            $table->unique(['criterios_id', 'estudiantes_id', 'calificacion_docentes_id'], 'unique_criterio_estudiante_calificacion');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('calificacion_docente_detalles');
    }
}
