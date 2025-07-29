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
        Schema::table('catalogo_camiones_acarreos', function (Blueprint $table) {
            $table->string('unidad_medida')->nullable()->after('nombre');
            $table->decimal('capacidad', 10, 2)->nullable()->after('unidad_medida');
            $table->decimal('capacidad_tonelada', 10, 2)->nullable()->after('capacidad');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('catalogo_camiones_acarreos', function (Blueprint $table) {
            $table->dropColumn(['unidad_medida', 'capacidad', 'capacidad_tonelada']);
        });
    }
};
