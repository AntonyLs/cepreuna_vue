<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSincronizarToCargaAcademicasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('carga_academicas', function (Blueprint $table) {
            $table->enum('sincronizar',['0','1','2'])->default("0")->comment("0=pendiente  1= sincronizado  2=error")->after('estado');
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
