<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComentariosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comentarios', function (Blueprint $table) {
            $table->id();
            $table->text('descripcion');
            $table->enum('tipo', ['1', '2'])->default('1')->comment('1:comentario  2:subcomentario');
            $table->enum('estado', ['0', '1'])->default('1')->comment('0:inactivo  1:activo');
            $table->bigInteger('user_id');
            $table->bigInteger('role_id');
            $table->unsignedBigInteger('comentario_id')->nullable();
            $table->unsignedBigInteger('publicacion_id');

            $table->timestamps();

            $table->foreign('comentario_id')->references('id')->on('comentarios');
            $table->foreign('publicacion_id')->references('id')->on('publicaciones');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comentarios');
    }
}
