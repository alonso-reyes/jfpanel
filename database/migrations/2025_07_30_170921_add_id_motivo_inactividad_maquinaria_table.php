<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Limpiar los valores actuales de la columna estado
        // DB::statement("UPDATE maquinarias SET estado = ACTIVO");

        // // 2. Modificar el enum con los nuevos valores
        // DB::statement("ALTER TABLE maquinarias MODIFY estado ENUM('ACTIVO', 'INACTIVO') NULL");

        Schema::table('maquinarias', function (Blueprint $table) {
            $table->unsignedBigInteger('motivo_inactividad_id')->nullable()->after('estado');

            $table->foreign('motivo_inactividad_id')
                ->references('id')
                ->on('catalogo_motivos_inactividad_maquinaria')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE maquinarias MODIFY estado ENUM('DISPONIBLE', 'EN USO', 'MANTENIMIENTO') NULL");

        Schema::table('maquinarias', function (Blueprint $table) {
            $table->dropForeign(['motivo_inactividad_id']);
            $table->dropColumn('motivo_inactividad_id');
        });
    }
};
