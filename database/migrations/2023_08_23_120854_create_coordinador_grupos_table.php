<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoordinadorGruposTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coordinador_grupos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('grupos_id')->nullable();
            $table->unsignedBigInteger('coordinador_id');

            $table->foreign('grupos_id')->references('id')->on('grupo_aulas');
            $table->foreign('coordinador_id')->references('id')->on('users');
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
        Schema::dropIfExists('coordinador_grupos');
    }
}
