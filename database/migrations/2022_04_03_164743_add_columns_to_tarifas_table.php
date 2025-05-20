<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToTarifasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tarifas', function (Blueprint $table) {
            $table->enum('modalidad', ["1", "2"])->comment("1:virtual 2:presencial")->after('concepto_pagos_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tarifas', function (Blueprint $table) {
            //
        });
    }
}
