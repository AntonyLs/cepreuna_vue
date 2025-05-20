<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNosotrosDescripcionDatos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nosotros_descripcion_datos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('descripcion');
            $table->enum('activo',['0','1'])->comment('0:no 1:si')->default('1');
            $table->unsignedBigInteger('nosotros_tipo_dato_id');
            $table->foreign('nosotros_tipo_dato_id')->references('id')->on('nosotros_tipos_datos');
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
        Schema::dropIfExists('nosotros_descripcion_datos');
    }
}
