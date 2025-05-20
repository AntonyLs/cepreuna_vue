<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateEstadoColumnToInscripciones extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::table('inscripciones', function (Blueprint $table) {
        //     //
        // });
        \DB::statement("ALTER TABLE inscripciones MODIFY COLUMN estado ENUM('0', '1', '2') COMMENT '0:pre inscrito  1:inscrito  2:retirado'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('inscripciones', function (Blueprint $table) {
            //
        });
    }
}
