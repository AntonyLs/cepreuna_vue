<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTarifaEstudiantesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tarifa_estudiantes', function (Blueprint $table) {
            $table->id();
            $table->decimal("monto", 10, 2);
            $table->decimal("pagado", 10, 2);
            $table->decimal("mora", 10, 2);
            $table->tinyInteger('nro_cuota');
            $table->enum('modalidad', ["1", "2"])->comment("1:virtual 2:presencial");
            $table->enum("tipo_estudiante", ["1", "2", "3", "4", "5", "6"])->comment("1:normal  2:hijo de trabajador 3:descuento trabajador UNA 4: descuento hermano 5: inscripcion por RR 6:servicio militar");
            $table->unsignedBigInteger('estudiantes_id');

            $table->timestamps();
            $table->foreign('estudiantes_id')->references('id')->on('estudiantes');
            $table->unique(['estudiantes_id', 'nro_cuota'], 'unique_estudiante_nro_cuota');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tarifa_estudiantes');
    }
}
