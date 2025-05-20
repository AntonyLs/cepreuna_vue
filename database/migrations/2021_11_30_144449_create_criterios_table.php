<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCriteriosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('criterios', function (Blueprint $table) {
            $table->id();
            $table->text("denominacion");
            $table->enum("tipo", ["1", "2"])->comment("1:docentes  2:estudiantes");
            $table->smallInteger("puntaje")->default("0");
            $table->enum("estado", ["0", "1"])->default('0')->comment("0:activo|1:inactivo");
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
        Schema::dropIfExists('criterios');
    }
}
