<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuxiliarCoordinadoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('auxiliar_coordinadores', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('auxiliares_id');
            $table->unsignedBigInteger('users_id');
            $table->timestamps();
            $table->foreign('auxiliares_id')->references('id')->on('auxiliares');
            $table->foreign('users_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('auxiliar_coordinadores');
    }
}
