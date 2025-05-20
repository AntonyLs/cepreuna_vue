<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCamposBancariosToDocentes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('docentes', function (Blueprint $table) {
            $table->char("ruc",11);
            $table->string("nombre_banco",50);
            $table->char("cci",20);
            $table->enum("dicto",["0","1"])->comment("0:No enseño  1:Si enseño");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('docentes', function (Blueprint $table) {
            $table->char("ruc",11);
            $table->string("nombre_banco",50);
            $table->char("cci",20);
        });
    }
}
