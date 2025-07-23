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
        Schema::table('acarreos_agua', function (Blueprint $table) {
            $table->decimal('capacidad', 10, 2)->nullable()->after('viajes');
            $table->decimal('volumen', 10, 2)->nullable()->after('capacidad');
            $table->decimal('volumen_compactado', 10, 2)->nullable()->after('volumen');
            $table->foreignId('concepto_id')
                ->nullable()
                ->after('observaciones')
                ->constrained('conceptos_presupuesto')
                ->onDelete('cascade');
            $table->decimal('factor_abundamiento', 10, 2)->nullable()->after('concepto_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('acarreos_agua', function (Blueprint $table) {
            $table->dropColumn('capacidad');
            $table->dropColumn('volumen');
            $table->dropColumn('volumen_compactado');
            $table->dropForeign(['concepto_id']);
            $table->dropColumn('concepto_id');
            $table->dropColumn('factor_abundamiento');
        });
    }
};
