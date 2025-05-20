<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInscripcionDocentesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inscripcion_docentes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('docentes_id');
            $table->unsignedBigInteger('periodos_id');
            $table->timestamps();

            $table->foreign('docentes_id')->references('id')->on('docentes');
            $table->foreign('periodos_id')->references('id')->on('periodos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inscripcion_docentes');
    }
}
