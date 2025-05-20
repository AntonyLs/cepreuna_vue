<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateColegiosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('colegios', function (Blueprint $table) {
            $table->id();
            $table->string("denominacion",100);
            $table->string("direccion",100);
            $table->string("departamento",150);
            $table->string("provincia",150);
            $table->string("distrito",150);
            $table->unsignedBigInteger('tipo_colegios_id');
            $table->timestamps();

            $table->foreign('tipo_colegios_id')->references('id')->on('tipo_colegios');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('colegios');
    }
}
