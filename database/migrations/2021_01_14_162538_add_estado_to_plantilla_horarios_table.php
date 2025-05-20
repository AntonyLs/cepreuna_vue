<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEstadoToPlantillaHorariosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('plantilla_horarios', function (Blueprint $table) {
            $table->enum('estado',["0","1"])->comment("0:activo 1:inactivo")->default("1")->after('tipo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('plantilla_horarios', function (Blueprint $table) {
            //
        });
    }
}
