<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateControlCronsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('control_crons', function (Blueprint $table) {
            $table->id();
            $table->integer("total_registros");
            $table->integer("ejecutado_registros");
            $table->smallInteger("tipo")->comment("1:matriculas|2:grupo aulas");
            $table->enum("estado", ["0", "1"])->default('0')->comment("0:activo|1:inactivo");
            $table->text("url");
            $table->unsignedBigInteger('users_id');

            $table->timestamps();

            $table->foreign('users_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('control_crons');
    }
}
