<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePublicacionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('publicaciones', function (Blueprint $table) {
            $table->id();
            $table->enum('tipo', ['1', '2'])->comment('1:publicacion  2:comunicado');
            $table->text('descripcion');
            $table->string('archivo')->nullable();
            $table->bigInteger('user_id');
            $table->bigInteger('role_id');
            $table->bigInteger('like');
            $table->enum('estado', ['0', '1'])->default('1')->comment('0:inactivo  1:activo');
            $table->string('imagen_pub')->nullable();
            $table->string('imagen_tumb')->nullable();
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
        Schema::dropIfExists('publicaciones');
    }
}
