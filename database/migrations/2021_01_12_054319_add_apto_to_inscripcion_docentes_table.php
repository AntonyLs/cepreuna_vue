<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAptoToInscripcionDocentesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inscripcion_docentes', function (Blueprint $table) {
            $table->enum('apto',["0","1"])->comment("0:No 1:SI")->default("0")->after('periodos_id');
            $table->float('puntaje')->after('apto');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('inscripcion_docentes', function (Blueprint $table) {
            //
        });
    }
}
