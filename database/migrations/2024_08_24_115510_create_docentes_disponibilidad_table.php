<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocentesDisponibilidadTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('docentes_disponibilidad', function (Blueprint $table) {
            $table->id();
            $table->char("nro_documento",30);
            $table->string("nombres",50);
            $table->string("paterno",50);
            $table->string("materno",50);
            $table->enum("edit", ["0", "1"])->default('0')->comment("0:no|1:si");
            $table->unsignedBigInteger('docentes_id');
            $table->timestamps();

            $table->foreign('docentes_id')->references('id')->on('docentes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('docentes_disponibilidad');
    }
}
