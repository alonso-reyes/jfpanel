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
        Schema::create('pipas', function (Blueprint $table) {
            $table->id();
            $table->string('numero_economico');
            $table->decimal('capacidad')->nullable();
            $table->decimal('horometro_inicial')->default(0);
            $table->decimal('horometro_final')->default(0);
            $table->enum('estado', ['ACTIVO', 'INACTIVO'])->default('ACTIVO');
            $table->unsignedBigInteger('motivo_inactividad_id')->nullable();
            $table->foreign('motivo_inactividad_id')
                ->references('id')
                ->on('catalogo_motivos_inactividad_maquinaria')
                ->nullOnDelete();
            $table->text('observaciones')->nullable();
            $table->foreignId('obra_id')->constrained('obras')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['numero_economico', 'obra_id'], 'pipa_numero_economico_obra_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pipas');
    }
};
