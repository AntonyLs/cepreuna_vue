<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUniqueToAsistenciaDocentesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('asistencia_docentes', function (Blueprint $table) {
            $table->unique(['fecha', 'docentes_id', 'carga_academicas_id'], 'unique_carga_fecha_docente');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('asistencia_docentes', function (Blueprint $table) {
            //
        });
    }
}
