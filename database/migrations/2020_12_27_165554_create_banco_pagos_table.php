<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBancoPagosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('banco_pagos', function (Blueprint $table) {
            $table->string('cod_uni',3);
            $table->string('num_mat',15);
            $table->string('secuencia',7);
            $table->string('tip_doc',2);
            $table->string('situacion',8);
            $table->string('concepto',8);
            $table->string('tip_per',2);
            $table->string('sede',2);
            $table->string('num_doc',15);
            $table->decimal('imp_pag',13,2);
            $table->string('tip_pag',1);
            $table->string('for_pag',1);
            $table->date('fch_pag');
            $table->string('hra_pag',6);
            $table->string('cod_caj',4);
            $table->string('cod_age',4);
            $table->string('num_che',8);
            $table->string('cod_ban',3);
            $table->string('con_pag',1);
            $table->date('fch_env');
            $table->string('nom_cli',35);
            $table->string('cuenta',10);
            $table->string('ano_aca',4);
            $table->string('per_aca',2);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('banco_pagos');
    }
}
