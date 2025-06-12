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
        Schema::create('camiones', function (Blueprint $table) {
            $table->id();
            $table->string('clave');
            $table->string('tipo');
            $table->decimal('largo', 8, 2);
            $table->decimal('ancho', 8, 2);
            $table->decimal('altura', 8, 2);
            $table->decimal('capacidad', 8, 2);
            $table->string('inspeccion_mecanica')->nullable();
            $table->string('propietario');
            $table->foreignId('obra_id')->constrained('obras')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['clave', 'obra_id'], 'camiones_clave_obra_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('camiones');
    }
};
