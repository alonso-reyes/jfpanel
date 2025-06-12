<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reportes_fotografias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reporte_frente_id')->constrained('reportes_jefe_frente')->onDelete('cascade');
            $table->string('url');
            $table->text('descripcion');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reportes_fotografias');
    }
};
