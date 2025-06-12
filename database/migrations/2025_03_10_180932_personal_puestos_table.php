<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('puestos', function (Blueprint $table) {
            $table->id();
            $table->string('puesto');
            $table->foreignId('obra_id')->constrained('obras')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('personal', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->foreignId('puesto_id')->constrained('puestos')->onDelete('cascade');
            $table->foreignId('obra_id')->constrained('obras')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('puestos');
        Schema::dropIfExists('personal');
    }
};
