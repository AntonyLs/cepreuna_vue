<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTipoTrabajadorContratoUbigeoToDocentesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('docentes', function (Blueprint $table) {
            $table->enum('tipo_trabajador', ["1","2"])->comment("1:Administrativo 2:Docente")->after('codigo_unap')->nullable();
            $table->enum('contrato', ["1","2"])->comment("1:Contratado 2:Nombrado")->after('tipo_trabajador')->nullable();
            $table->unsignedBigInteger('ubigeos_id')->after('contrato')->nullable();

            $table->foreign('ubigeos_id')->references('id')->on('ubigeos');
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
            //
        });
    }
}
