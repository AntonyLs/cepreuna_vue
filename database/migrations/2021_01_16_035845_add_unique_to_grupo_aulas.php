<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUniqueToGrupoAulas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('grupo_aulas', function (Blueprint $table) {
            $table->unique(['grupos_id','aulas_id','periodos_id'],'unique_grupo_aula_periodo');
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
            //
        });
    }
}
