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
        Schema::table('conceptos_presupuesto', function (Blueprint $table) {
            $table->string('tipo')->nullable()->after('id');
            $table->decimal('factor_abundamiento', 10, 2)->nullable()->after('cantidad');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conceptos_presupuesto', function (Blueprint $table) {
            $table->dropColumn(['tipo']);
            $table->dropColumn(['factor_abundamiento']);
        });
    }
};
