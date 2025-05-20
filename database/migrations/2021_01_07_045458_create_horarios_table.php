<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHorariosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('horarios', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('carga_academicas_id');
            $table->unsignedBigInteger('plantilla_horarios_id');
            $table->enum("dia",["1","2","3","4","5"])->comment("1:lunes 2:martes ... 5:viernes");
            $table->timestamps();

            $table->foreign('carga_academicas_id')->references('id')->on('carga_academicas');
            $table->foreign('plantilla_horarios_id')->references('id')->on('plantilla_horarios');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('horarios');
    }
}
