<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSecundariaUniversidadToEstudiantesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('estudiantes', function (Blueprint $table) {

            $table->enum('estado_colegio', ["1", "2"])->comment("1:si 2:no")->nullable()->after('path_dni');
            $table->enum('estado_universidad', ["1", "2"])->comment("1:si 2:no ")->nullable()->after('estado_colegio');
            $table->enum('estado_discapacidad', ["0", "1"])->comment("0:no 1:si")->nullable()->default("0")->after('estado_universidad');
            $table->string('discapacidad', 250)->nullable()->after('estado_discapacidad');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('estudiantes', function (Blueprint $table) {
            //
        });
    }
}
