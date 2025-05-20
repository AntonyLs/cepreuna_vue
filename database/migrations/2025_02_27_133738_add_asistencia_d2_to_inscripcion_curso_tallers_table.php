<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAsistenciaD2ToInscripcionCursoTallersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inscripcion_curso_tallers', function (Blueprint $table) {
            $table->boolean("asistencia_d2")->default(false);
            $table->boolean("asistencia_d1_2")->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('inscripcion_curso_tallers', function (Blueprint $table) {
            //
        });
    }
}
