<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocentesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('docentes', function (Blueprint $table) {
            $table->id();
            $table->string("nombres",50);
            $table->string("paterno",50);
            $table->string("materno",50);
            $table->char("nro_documento",30);
            $table->enum("condicion",["1","2"])->comment("1:particula  2:Unap");
            $table->string("profesion",100);
            $table->string("direccion",50)->nullable();
            $table->char("celular",9);
            $table->string("email",50);
            $table->string("foto",100)->nullable();
            $table->char("codigo_unap",20)->nullable();
            $table->string("usuario",100);
            $table->string("password",8);
            $table->string("idgsuite",30);
            $table->enum("estado",["0","1"])->comment("0:inactivo  1:activo")->default('1');
            $table->unsignedBigInteger('tipo_documentos_id');
            $table->unsignedBigInteger('grado_academicos_id');
            $table->unsignedBigInteger('programas_id');
            $table->timestamps();

            $table->foreign('tipo_documentos_id')->references('id')->on('tipo_documentos');
            $table->foreign('grado_academicos_id')->references('id')->on('grado_academicos');
            $table->foreign('programas_id')->references('id')->on('programas');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('docentes');
    }
}
