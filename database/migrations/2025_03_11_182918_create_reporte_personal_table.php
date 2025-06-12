<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reportes_personal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reporte_frente_id')->constrained('reportes_jefe_frente')->onDelete('cascade');
            $table->foreignId('personal_id')->constrained('personal')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reportes_personal');
    }
};
