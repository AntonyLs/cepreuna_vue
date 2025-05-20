<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToAsistenciaEstudiantesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('asistencia_estudiantes', function (Blueprint $table) {
            $table->enum('estado', ["0", "1"])->default("1")->comment("0:cerrado 1:abierto")->after('observacion');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('asistencia_estudiantes', function (Blueprint $table) {
            //
        });
    }
}
