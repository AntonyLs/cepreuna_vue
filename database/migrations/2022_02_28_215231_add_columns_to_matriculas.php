<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToMatriculas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('matriculas', function (Blueprint $table) {
            $table->enum("habilitado", ["0", "1"])->default('0')->comment("0:pendiente|1:habilitado")->after('estado');
            $table->enum("habilitado_estado", ["0", "1", "2"])->default('0')->comment("0:pendiente|1:sincronizado|2:error")->after('habilitado');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('matriculas', function (Blueprint $table) {
            //
        });
    }
}
