<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEstadoToProgramasTable extends Migration
{
    /**
     *
     * @return void
     */
    public function up()
    {
        Schema::table('programas', function (Blueprint $table) {
            $table->enum('estado', ['0', '1'])->comment("0:no|1:si")->default('0');
        });
    }

    /**
     *
     * @return void
     */
    public function down()
    {
        Schema::table('programas', function (Blueprint $table) {
            $table->dropColumn('estado');
        });
    }
}
