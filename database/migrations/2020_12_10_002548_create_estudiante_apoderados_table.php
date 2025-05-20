<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEstudianteApoderadosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('estudiante_apoderados', function (Blueprint $table) {
            $table->id();
            $table->enum("estado",["0","1"])->commet("0:inactivo  1:activo");
            $table->unsignedBigInteger('estudiantes_id');
            $table->unsignedBigInteger('apoderados_id');
            $table->timestamps();

            $table->foreign('estudiantes_id')->references('id')->on('estudiantes');
            $table->foreign('apoderados_id')->references('id')->on('apoderados');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('estudiante_apoderados');
    }
}
