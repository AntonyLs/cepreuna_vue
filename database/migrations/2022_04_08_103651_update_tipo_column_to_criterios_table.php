<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTipoColumnToCriteriosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('criterios', function (Blueprint $table) {
            \DB::statement("ALTER TABLE criterios MODIFY COLUMN tipo ENUM('1', '2', '3') COMMENT '1:docentes  2:estudiantes 3:inscripcion docente'");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('criterios', function (Blueprint $table) {
            //
        });
    }
}
