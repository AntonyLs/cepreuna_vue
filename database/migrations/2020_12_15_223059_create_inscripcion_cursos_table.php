<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInscripcionCursosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inscripcion_cursos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('inscripcion_docentes_id');
            $table->unsignedBigInteger('cursos_id');
            $table->timestamps();

            $table->foreign('inscripcion_docentes_id')->references('id')->on('inscripcion_docentes');
            $table->foreign('cursos_id')->references('id')->on('cursos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inscripcion_cursos');
    }
}
