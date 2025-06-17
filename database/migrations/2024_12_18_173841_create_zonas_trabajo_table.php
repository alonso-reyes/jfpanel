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
        Schema::create('zonas_trabajo', function (Blueprint $table) {
            $table->id(); // Columna id auto-incremental
            $table->string('clave', 50)->unique();
            $table->string('nombre', 100);
            $table->string('descripcion', 255)->nullable();
            $table->string('imagen')->nullable();
            $table->string('cloudinary_public_id')->nullable();
            $table->foreignId('obra_id')->constrained('obras')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('zonas_trabajo');
    }
};
