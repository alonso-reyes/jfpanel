<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reportes_maquinaria', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reporte_frente_id')->constrained('reportes_jefe_frente')->onDelete('cascade');
            $table->foreignId('concepto_id')->constrained('conceptos_presupuesto')->onDelete('cascade');
            $table->foreignId('tipo_maquinaria_id')->constrained('tipos_maquinaria')->onDelete('cascade');
            $table->foreignId('maquinaria_id')->constrained('maquinarias')->onDelete('cascade');
            $table->foreignId('operador_id')->nullable();
            $table->text('observaciones')->nullable();
            $table->decimal('horometro_inicial')->nullable();
            $table->decimal('horometro_final')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reportes_maquinaria');
    }
};
