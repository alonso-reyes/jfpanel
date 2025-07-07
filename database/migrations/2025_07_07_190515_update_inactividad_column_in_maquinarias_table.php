<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateInactividadColumnInMaquinariasTable extends Migration
{
    public function up()
    {
        Schema::table('maquinarias', function (Blueprint $table) {
            $table->enum('inactividad', [
                'ninguna',
                'mantenimiento',
                'falta de operador',
                'falta de tramo',
                'condiciones climaticas'
            ])->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('maquinarias', function (Blueprint $table) {
            $table->enum('inactividad', [
                'mantenimiento',
                'falta de operador',
                'falta de tramo',
                'condiciones climaticas'
            ])->nullable()->change();
        });
    }
}
