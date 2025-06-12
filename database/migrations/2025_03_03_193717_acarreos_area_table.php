<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('acarreos_area', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reporte_frente_id')->constrained('reportes_jefe_frente')->onDelete('cascade');
            // $table->integer('viajes');
            $table->decimal('largo', 8, 2);
            $table->decimal('ancho', 8, 2);
            $table->decimal('area', 8, 2);
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('acarreos_area');
    }
};
