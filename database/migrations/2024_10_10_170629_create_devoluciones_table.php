<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDevolucionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('devoluciones', function (Blueprint $table) {
            $table->id();
            $table->string("nombres",150);
            $table->char("nro_documento",30);
            $table->date('fecha');
            $table->char("nro_registro",30);
            $table->enum('procede', ['0', '1', '2'])->comment("0:no|1:si|2:en tramite")->nullable();

            $table->unsignedBigInteger('pagos_id');
            $table->foreign('pagos_id')->references('id')->on('pagos');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('devoluciones');
    }
}
