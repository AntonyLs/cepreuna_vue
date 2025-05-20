<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReclamosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reclamos', function (Blueprint $table) {
            $table->id();
            $table->string('nombres',45);
            $table->string('paterno',45);
            $table->string('materno',45);
            $table->char('dni',30);
            $table->string('correo',45);
            $table->string('domicilio',45)->nullable();
            $table->string('celular',45);
            $table->string('apoderado',50)->nullable();
            $table->string('descripcion',100);
            $table->enum('tipo_reclamacion', ["0", "1"])->default("0")->comment("0:Queja 1:Reclamo");
            $table->string('detalle',100);
            $table->string('pedido',100);
            $table->string('path',100);
            $table->enum('estado', ["0", "1"])->default("0")->comment("0:Enviado 1:Atendido");
            $table->date('fecha_ingreso');
            $table->string('respuesta',100);
            $table->date('fecha_respuesta');
            $table->bigInteger('user_id')->nullable();
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
        Schema::dropIfExists('reclamos');
    }
}
