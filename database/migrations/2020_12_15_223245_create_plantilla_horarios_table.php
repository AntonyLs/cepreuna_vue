<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlantillaHorariosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plantilla_horarios', function (Blueprint $table) {
            $table->id();
            $table->time("hora_inicio");
            $table->time("hora_fin");
            $table->enum("tipo",["1","2"])->comment("1:normal 2:receso");
            $table->unsignedBigInteger('turnos_id');
            $table->timestamps();

            $table->foreign('turnos_id')->references('id')->on('turnos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('plantilla_horarios');
    }
}
