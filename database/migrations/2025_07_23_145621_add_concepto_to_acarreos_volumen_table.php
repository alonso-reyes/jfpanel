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
        Schema::table('acarreos_volumen', function (Blueprint $table) {
            $table->foreignId('concepto_id')
                ->nullable()
                ->after('observaciones')
                ->constrained('conceptos_presupuesto')
                ->onDelete('cascade');
            $table->decimal('factor_abundamiento', 10, 2)->nullable()->after('concepto_id');
            $table->decimal('volumen_compactado', 10, 2)->nullable()->after('volumen');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('acarreos_volumen', function (Blueprint $table) {
            $table->dropForeign(['concepto_id']);
            $table->dropColumn('concepto_id');
            $table->dropColumn('factor_abundamiento');
            $table->dropColumn('volumen_compactado');
        });
    }
};
