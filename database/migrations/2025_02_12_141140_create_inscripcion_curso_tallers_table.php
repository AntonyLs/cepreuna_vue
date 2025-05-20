<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInscripcionCursoTallersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inscripcion_curso_tallers', function (Blueprint $table) {
            $table->id();
            //datos
            $table->string("nombres",50);
            $table->string("paterno",50);
            $table->string("materno",50);
            $table->unsignedBigInteger('tipo_documentos_id');
            $table->char("nro_documento",30);
            $table->char("celular",9);
            $table->string("email",50);
            $table->enum("area",["1","2","3","4"])->comment("1:matematicas  2:lenguaje 3:ciencias 4:sociales");
            $table->enum("condicion",["1","2"])->comment("1:unap  2:particular");
            //detalles
            $table->decimal("monto",10,2);
            $table->string("secuencia",10);
            
            $table->char("codigoqr",30);
            $table->unsignedBigInteger('pagos_id');
            $table->text("path")->nullable();
            $table->boolean("asistencia")->default(false);

            $table->foreign('tipo_documentos_id')->references('id')->on('tipo_documentos');
            $table->foreign('pagos_id')->references('id')->on('pagos');
           
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
        Schema::dropIfExists('inscripcion_curso_tallers');
    }
}
