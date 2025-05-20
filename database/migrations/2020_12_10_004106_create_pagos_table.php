<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePagosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->string("secuencia",10);
            $table->decimal("monto",10,2);
            $table->date("fecha");
            $table->char("nro_documento",30);
            $table->string("token",100);
            $table->enum("tipo_pago",["1","2"])->comment("1:deposito bancario  2:descuento");
            $table->text('constancia')->nullable();
            $table->enum("estado",["1","2"])->comment("1:pendiente  2:usado");
            $table->text("voucher")->nullable();
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
        Schema::dropIfExists('pagos');
    }
}
