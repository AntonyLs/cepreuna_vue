<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfiguracionInscripcionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('configuracion_inscripciones', function (Blueprint $table) {
            $table->id();
            $table->dateTime('inicio');
            $table->dateTime('fin');
            $table->enum("tipo_inscripcion",["1","2"])->comment("1:normal 2:extemporaneo");
            $table->enum("estado",["0","1"])->comment("0:inactivo 1:activo");
            $table->enum("tipo_usuario",["1","2"])->comment("1:estudiante 2:docente");
            $table->text("observacion");
            $table->unsignedBigInteger('users_id');
            $table->unsignedBigInteger('periodos_id');
            $table->timestamps();

            $table->foreign('users_id')->references('id')->on('users');
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
        Schema::dropIfExists('configuracion_inscripciones');
    }
}
