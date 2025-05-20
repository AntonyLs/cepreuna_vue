<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddModalidadToCalificacionDocentesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('calificacion_docentes', function (Blueprint $table) {
            $table->enum('modalidad', ['0', '1'])->default('0')->comment('0:presencial 1:virtual');
            $table->decimal('puntaje_total')->default("0");
            $table->decimal('observacion')->default("0");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('calificacion_docentes', function (Blueprint $table) {
            //
        });
    }
}
