<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSincronizarCorreoToInscripcionDocentesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inscripcion_docentes', function (Blueprint $table) {
            $table->enum('sincronizar_correo', ["0", "1","2"])->default("0")->comment("0:No 1:Si 2:error")->after('sincronizar');
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
