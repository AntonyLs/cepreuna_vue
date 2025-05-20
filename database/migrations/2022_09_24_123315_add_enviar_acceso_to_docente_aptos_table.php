<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEnviarAccesoToDocenteAptosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('docente_aptos', function (Blueprint $table) {
            $table->enum('enviar_acceso', ["0", "1"])->default("0")->comment("0:NO 1:SI")->after('sincronizar');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('docente_aptos', function (Blueprint $table) {
            //
        });
    }
}
