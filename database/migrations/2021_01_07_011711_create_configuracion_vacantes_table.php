<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfiguracionVacantesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('configuracion_vacantes', function (Blueprint $table) {
            $table->id();
            $table->enum("estado",["0","1"])->comment("0:inactivo 1:activo");
            $table->integer('cantidad');
            $table->unsignedBigInteger('turnos_id');
            $table->unsignedBigInteger('areas_id');
            $table->unsignedBigInteger('sedes_id');
            $table->timestamps();

            $table->foreign('turnos_id')->references('id')->on('turnos');
            $table->foreign('areas_id')->references('id')->on('areas');
            $table->foreign('sedes_id')->references('id')->on('sedes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('configuracion_vacantes');
    }
}
