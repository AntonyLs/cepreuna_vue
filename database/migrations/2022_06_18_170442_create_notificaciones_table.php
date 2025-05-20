<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificacionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notificaciones', function (Blueprint $table) {
            $table->id();
            $table->enum('tipo', ['1', '2'])->default('1')->comment('1:publicacion  2:comentario');
            $table->enum('estado', ['0', '1'])->default('0')->comment('0:novisto  1:visto');
            $table->bigInteger('user_id');
            $table->bigInteger('role_id');
            $table->text('descripcion');
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
        Schema::dropIfExists('notificaciones');
    }
}
