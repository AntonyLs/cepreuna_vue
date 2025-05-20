<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdjuntoProduccionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('adjunto_producciones', function (Blueprint $table) {
            $table->id();
            $table->string("titulo",200);
            $table->text("path");
            $table->unsignedBigInteger('producciones_id');
            $table->unsignedBigInteger('inscripcion_docentes_id');
            $table->timestamps();
            $table->foreign('inscripcion_docentes_id')->references('id')->on('inscripcion_docentes');
            $table->foreign('producciones_id')->references('id')->on('producciones');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('adjunto_producciones');
    }
}
