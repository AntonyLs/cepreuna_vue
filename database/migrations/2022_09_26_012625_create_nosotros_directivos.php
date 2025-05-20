<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNosotrosDirectivos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nosotros_directivos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->enum('tipo',['1','2'])->comment('1:presidente 2:miembro')->default('2');
            $table->string('sigla_grado_academico');
            $table->string('nombres');
            $table->string('paterno');
            $table->string('materno');
            $table->string('foto_path');
            $table->enum('activo',['0','1'])->comment('0:no 1:si')->default('1');
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
        Schema::dropIfExists('nosotros_directivos');
    }
}
