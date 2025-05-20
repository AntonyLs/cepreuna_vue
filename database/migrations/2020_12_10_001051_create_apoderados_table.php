<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApoderadosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('apoderados', function (Blueprint $table) {
            $table->id();
            $table->string("nombres",50);
            $table->string("paterno",50);
            $table->string("materno",50);
            $table->char("celular",9);
            $table->unsignedBigInteger('parentescos_id');
            $table->timestamps();

            $table->foreign('parentescos_id')->references('id')->on('parentescos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('apoderados');
    }
}
