<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('acarreos_volumen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reporte_frente_id')->constrained('reportes_jefe_frente')->onDelete('cascade');
            $table->foreignId('material_id')->constrained('materiales')->onDelete('cascade');
            $table->foreignId('material_uso_id')->constrained('materiales_uso')->onDelete('cascade');
            $table->foreignId('origen_id')->constrained('origenes')->onDelete('cascade');
            $table->foreignId('destino_id')->constrained('destinos')->onDelete('cascade');
            // $table->foreignId('camion_id')->constrained('camiones')->onDelete('cascade');
            $table->foreignId('camion_id')->constrained('catalogo_camiones_acarreos')->onDelete('cascade');
            $table->integer('viajes');
            $table->decimal('capacidad', 10, 2);
            $table->decimal('volumen', 10, 2);
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('acarreos_volumen');
    }
};
