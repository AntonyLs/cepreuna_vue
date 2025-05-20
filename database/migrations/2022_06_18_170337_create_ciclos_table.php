<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCiclosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ciclos', function (Blueprint $table) {
            $table->id();
            $table->string('inicio_ciclo', 50);
            $table->string('fin_ciclo', 50);
            $table->date('inicio_inscripciones');
            $table->date('fin_inscripciones');
            $table->date('inicio_clases');
            $table->enum('estado', ['0', '1'])->default('1')->comment('0:inactivo  1:activo');
            $table->string('path');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ciclos');
    }
}
