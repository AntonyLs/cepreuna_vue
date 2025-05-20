<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEstudiantesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('estudiantes', function (Blueprint $table) {
            $table->id();
            $table->string("nombres",50);
            $table->string("paterno",50);
            $table->string("materno",50);
            $table->char("nro_documento",30);
            $table->date("fecha_nac");
            $table->char("celular",9);
            $table->string("email",50);
            $table->enum("sexo",["1","2"])->comment("1:masculino  2:femenino");
            $table->char("anio_egreso",4);
            $table->string("foto",100);
            $table->string("usuario",100);
            $table->string("password",8);
            $table->string("idgsuite",30)->nullable();
            $table->enum("estado",["0","1"])->comment("0:inactivo  1:activo")->default('1');
            $table->unsignedBigInteger('pais_id');
            $table->unsignedBigInteger('ubigeos_id')->nullable();
            $table->unsignedBigInteger('tipo_documentos_id');
            $table->unsignedBigInteger('colegios_id');
            $table->timestamps();

            $table->foreign('pais_id')->references('id')->on('pais');
            $table->foreign('ubigeos_id')->references('id')->on('ubigeos');
            $table->foreign('tipo_documentos_id')->references('id')->on('tipo_documentos');
            $table->foreign('colegios_id')->references('id')->on('colegios');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('estudiantes');
    }
}
