<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToInscripcionDocentesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inscripcion_docentes', function (Blueprint $table) {
            $table->enum('modalidad',["1","2","3"])->comment("1:virtual 2:presencial 3:ambas")->after('puntaje');
            $table->unsignedBigInteger('areas_id')->nullable()->after('modalidad');
            $table->foreign('areas_id')->references('id')->on('areas');
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
