<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDisponibilidadesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('disponibilidades', function (Blueprint $table) {
            $table->id();
            $table->enum("prioridad",["1","2"])->comment("1:alta 2:media");
            $table->enum("dia",["1","2","3","4","5"])->comment("1:lunes 2:martes ... 5:viernes");
            $table->unsignedBigInteger('plantilla_horarios_id');
            $table->unsignedBigInteger('sedes_id');
            $table->unsignedBigInteger('inscripcion_docentes_id');
            $table->timestamps();

            $table->foreign('plantilla_horarios_id')->references('id')->on('plantilla_horarios');
            $table->foreign('sedes_id')->references('id')->on('sedes');
            $table->foreign('inscripcion_docentes_id')->references('id')->on('inscripcion_docentes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('disponibilidades');
    }
}
