<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSincronizarToDocenteAptosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('docente_aptos', function (Blueprint $table) {
            $table->enum('sincronizar', ["0", "1","2"])->default("0")->comment("0:No 1:Si 2:error")->after('idgsuite');
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
