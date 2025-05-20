<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCurriculaDetallesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('curricula_detalles', function (Blueprint $table) {
            $table->id();
            $table->smallInteger("horas");
            $table->unsignedBigInteger('cursos_id');
            $table->unsignedBigInteger('curriculas_id');
            $table->timestamps();

            $table->foreign('cursos_id')->references('id')->on('cursos');
            $table->foreign('curriculas_id')->references('id')->on('curriculas');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('curricula_detalles');
    }
}
