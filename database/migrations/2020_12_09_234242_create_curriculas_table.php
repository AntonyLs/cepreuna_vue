<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCurriculasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('curriculas', function (Blueprint $table) {
            $table->id();
            $table->string("resolucion",50);
            $table->enum("estado",["0","1"])->comment('0:inactivo  1:activo');
            $table->enum("tipo",["1","2"])->comment('1:presencial  2:virtual');
            $table->unsignedBigInteger('areas_id');
            $table->timestamps();

            $table->foreign('areas_id')->references('id')->on('areas');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('curriculas');
    }
}
