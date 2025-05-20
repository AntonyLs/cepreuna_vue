<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            //
            $table->string('paterno','100')->nullable()->after('name');
            $table->string('materno','100')->nullable()->after('paterno');
            $table->string('dni','20')->nullable()->after('materno');
            $table->enum('estado',["0","1"])->comment("0:activo 1:inactivo")->default("1")->after('dni');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
}
