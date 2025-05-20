<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEstadoEncuestaToGrupoAulasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('grupo_aulas', function (Blueprint $table) {
            $table->enum('estado_encuesta', ['0', '1'])->default('0')->comment('0:inactivo 1:activo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('grupo_aulas', function (Blueprint $table) {
            $table->dropColumn('estado_encuesta');
        });
    }
}
