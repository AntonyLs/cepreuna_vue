<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToCurriculaDetallesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('curricula_detalles', function (Blueprint $table) {
            $table->enum('horario_inscripcion',["0","1"])->comment("0:NO 1:Si")->default('0')->after('horas');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('curricula_detalles', function (Blueprint $table) {
            //
        });
    }
}
