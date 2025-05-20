<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToInscripcionCursosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inscripcion_cursos', function (Blueprint $table) {
            $table->unsignedBigInteger('curricula_detalles_id')->nullable()->after('cursos_id');
            $table->unsignedBigInteger('cursos_id')->nullable()->change();
            $table->foreign('curricula_detalles_id')->references('id')->on('curricula_detalles');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('inscripcion_cursos', function (Blueprint $table) {
            //
        });
    }
}
