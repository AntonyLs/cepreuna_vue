<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBuDocenteEstudiantesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bu_docente_estudiantes', function (Blueprint $table) {
            $table->id();
            $table->string('d_dni', 10);
            $table->string('d_nombres', 50);
            $table->string('d_paterno', 50);
            $table->string('d_materno', 50);
            $table->string('grupo', 10);
            $table->string('curso');
            $table->string('e_dni', 10);
            $table->string('e_nombres', 50);
            $table->string('e_paterno', 50);
            $table->string('e_materno', 50);
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
        Schema::dropIfExists('bu_docente_estudiantes');
    }
}
