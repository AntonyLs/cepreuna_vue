<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToAsistenciaDocentesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('asistencia_docentes', function (Blueprint $table) {
            $table->smallInteger('cantidad_estudiantes')->nullable()->after('cantidad_horas');
            $table->text('path_imagen')->nullable()->after('cantidad_estudiantes');
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
