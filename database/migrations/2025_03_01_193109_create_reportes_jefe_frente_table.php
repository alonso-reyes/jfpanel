<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('reportes_jefe_frente', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuarios_jefe_frente')->onDelete('cascade');
            $table->foreignId('turno_id')->constrained('turnos')->onDelete('cascade');
            $table->time('hora_inicio_real_actividades');
            $table->time('hora_termino_real_actividades');
            $table->foreignId('zona_trabajo_id')->constrained('zonas_trabajo')->onDelete('cascade');
            $table->foreignId('obra_id')->constrained('obras')->onDelete('cascade');
            $table->text('sobrestante')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reportes_jefe_frente');
    }
};
