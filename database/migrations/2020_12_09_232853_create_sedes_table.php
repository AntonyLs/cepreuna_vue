<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSedesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sedes', function (Blueprint $table) {
            $table->id();
            $table->string("denominacion",100);
            $table->string("direccion",100);
            $table->enum('estado', ['0', '1'])->comment('0:inactivo 1:activo');
            $table->unsignedBigInteger('ubigeos_id');
            $table->timestamps();

            $table->foreign('ubigeos_id')->references('id')->on('ubigeos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sedes');
    }
}
