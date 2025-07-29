<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('reportes_maquinaria', function (Blueprint $table) {
            // Primero eliminamos la restricción foreign key
            $table->dropForeign('reportes_maquinaria_concepto_id_foreign');
        });

        Schema::table('reportes_maquinaria', function (Blueprint $table) {
            // Modificamos la columna para que sea nullable
            $table->foreignId('concepto_id')->nullable()->change();
        });

        Schema::table('reportes_maquinaria', function (Blueprint $table) {
            // Re-agregamos la restricción foreign key con onDelete cascade
            $table->foreign('concepto_id')->references('id')->on('conceptos_presupuesto')->onDelete('cascade');
        });

        Schema::table('reportes_maquinaria', function (Blueprint $table) {
            // $table->enum('motivo_inactividad', [
            //     'ninguna',
            //     'mantenimiento',
            //     'falta de operador',
            //     'falta de tramo',
            //     'condiciones climaticas'
            // ])->nullable()->after('observaciones');
            $table->unsignedBigInteger('motivo_inactividad_id')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('reportes_maquinaria', function (Blueprint $table) {
            // Revertimos: eliminamos FK
            $table->dropForeign('reportes_maquinaria_concepto_id_foreign');
        });

        Schema::table('reportes_maquinaria', function (Blueprint $table) {
            // Hacemos que no sea nullable
            $table->foreignId('concepto_id')->nullable(false)->change();
        });

        Schema::table('reportes_maquinaria', function (Blueprint $table) {
            // Re-agregamos la FK
            $table->foreign('concepto_id')->references('id')->on('conceptos_presupuesto')->onDelete('cascade');
        });

        Schema::table('reportes_maquinaria', function (Blueprint $table) {
            $table->dropColumn('motivo_inactividad_id');
        });
    }
};
