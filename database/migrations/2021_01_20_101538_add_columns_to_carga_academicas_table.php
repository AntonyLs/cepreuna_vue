<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToCargaAcademicasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('carga_academicas', function (Blueprint $table) {
            $table->enum('estado',["0","1"])->comment("0:inactivo 1:activado")->default("1")->after('periodos_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('carga_academicas', function (Blueprint $table) {
            //
        });
    }
}
