<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInscripcionSimulacrosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inscripcion_simulacros', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('estudiantes_id');
            $table->decimal("monto",10,2);
            $table->string("secuencia",10);
            $table->char("nro_documento",30);
            $table->char("codigoqr",30);
            $table->unsignedBigInteger('pagos_id');
            $table->text("path")->nullable();

            $table->foreign('pagos_id')->references('id')->on('pagos');
            $table->foreign('estudiantes_id')->references('id')->on('estudiantes');
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
        Schema::dropIfExists('inscripcion_simulacros');
    }
}
