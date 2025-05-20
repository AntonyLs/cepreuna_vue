<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNroDocumentoAndAccesoToApoderadosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('apoderados', function (Blueprint $table) {
            $table->string('nro_documento', 20)->nullable()->after('id');
            $table->smallInteger('acceso')->default(0)->after('celular');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('apoderados', function (Blueprint $table) {
            //
        });
    }
}
