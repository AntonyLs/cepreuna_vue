<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCuadernillosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cuadernillos', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('semana');
            $table->text('path');
            $table->enum('tipo', ['1', '2'])->default('1')->comment('1:docente 2:estudiante');
            $table->unsignedBigInteger('periodos_id');
            $table->unsignedBigInteger('curricula_detalles_id');
            $table->timestamps();

            $table->foreign('periodos_id')->references('id')->on('periodos');
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
        Schema::dropIfExists('cuadernillos');
    }
}
