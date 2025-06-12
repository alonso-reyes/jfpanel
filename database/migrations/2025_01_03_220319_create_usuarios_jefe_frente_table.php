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
        Schema::create('usuarios_jefe_frente', function (Blueprint $table) {
            $table->id();
            $table->enum('tipo_usuario', ['JEFE DE FRENTE', 'SUPERINTENDENTE'])->default('JEFE DE FRENTE');
            $table->string('nombre');
            $table->string('usuario')->unique();
            $table->string('password');
            $table->string('email')->nullable();
            $table->foreignId('obra_id')->constrained('obras')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuarios_jefe_frente');
    }
};
