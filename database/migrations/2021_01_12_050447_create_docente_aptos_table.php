<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocenteAptosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('docente_aptos', function (Blueprint $table) {
            $table->id();
            // $table->float('puntaje');
            $table->string('usuario',100);
            $table->string('password',100);
            $table->string("idgsuite",30)->nullable();
            $table->unsignedBigInteger('docentes_id');
            $table->unsignedBigInteger('periodos_id');
            $table->timestamps();

            $table->foreign('docentes_id')->references('id')->on('docentes');
            $table->foreign('periodos_id')->references('id')->on('periodos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('docente_aptos');
    }
}
