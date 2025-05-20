<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTemariosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('temarios', function (Blueprint $table) {
            $table->id();
            $table->text('path');
            $table->unsignedBigInteger('periodos_id');
            $table->unsignedBigInteger('curricula_detalles_id');
            $table->timestamps();

            $table->foreign('periodos_id')->references('id')->on('periodos');
            $table->foreign('curricula_detalles_id')->references('id')->on('curricula_detalles');
            $table->unique(['periodos_id','curricula_detalles_id'],'unique_periodo_curricula');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('temarios');
    }
}
