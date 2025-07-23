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
            $table->decimal('horas_efectivas')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reportes_maquinaria', function (Blueprint $table) {
            $table->dropColumn('horas_efectivas');
        });
    }
};
