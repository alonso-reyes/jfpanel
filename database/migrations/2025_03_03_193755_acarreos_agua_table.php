<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('acarreos_agua', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reporte_frente_id')->constrained('reportes_jefe_frente')->onDelete('cascade');
            $table->foreignId('maquinaria_id')->constrained('maquinarias')->onDelete('cascade');
            $table->foreignId('origen_id')->constrained('origenes')->onDelete('cascade');
            $table->foreignId('destino_id')->constrained('destinos')->onDelete('cascade');
            $table->integer('viajes');
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('acarreos_agua');
    }
};
