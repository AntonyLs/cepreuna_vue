<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSincronizarToInscripcionDocentesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inscripcion_docentes', function (Blueprint $table) {
            $table->enum('sincronizar', ["0", "1","2"])->default("0")->comment("0:No 1:Si 2:error")->after('areas_id');
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
