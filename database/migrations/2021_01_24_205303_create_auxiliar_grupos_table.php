<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuxiliarGruposTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('auxiliar_grupos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('auxiliares_id');
            $table->unsignedBigInteger('grupo_aulas_id');
            $table->timestamps();

            $table->foreign('auxiliares_id')->references('id')->on('auxiliares');
            $table->foreign('grupo_aulas_id')->references('id')->on('grupo_aulas');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('auxiliar_grupos');
    }
}
