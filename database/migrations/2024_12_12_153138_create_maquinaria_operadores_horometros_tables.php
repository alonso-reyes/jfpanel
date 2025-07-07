<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Tabla tipos_maquinaria
        Schema::create('tipos_maquinaria', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->foreignId('obra_id')->constrained('obras')->onDelete('cascade');
            $table->integer('acarreo_agua')->nullable();
            $table->integer('columna_excel')->nullable();
            $table->timestamps();

            $table->unique(['nombre', 'obra_id'], 'tipos_maquinaria_nombre_obra_unique');
        });

        // Tabla operadores
        Schema::create('operadores', function (Blueprint $table) {
            $table->id();
            $table->string('clave_trabajador')->nullable();
            $table->string('nombre');
            $table->foreignId('obra_id')->constrained('obras')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['clave_trabajador', 'obra_id'], 'operadores_clave_trabajador_obra_unique');
        });

        // Tabla maquinaria
        Schema::create('maquinarias', function (Blueprint $table) {
            $table->id();
            $table->string('numero_economico');
            $table->string('nombre')->nullable();
            $table->string('modelo');
            $table->foreignId('tipo_maquinaria_id')->constrained('tipos_maquinaria')->onDelete('cascade');
            $table->decimal('capacidad')->nullable();
            $table->decimal('horometro_inicial')->default(0);
            $table->decimal('horometro_final')->default(0);
            $table->enum('estado', ['activo', 'mantenimiento', 'inactivo'])->default('activo');
            $table->enum('inactividad', ['ninguna', 'mantenimiento', 'falta de operador', 'falta de tramo', 'condiciones climaticas'])->nullable();
            $table->text('observaciones_inactividad')->nullable();
            $table->text('observaciones')->nullable();
            $table->foreignId('obra_id')->constrained('obras')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['numero_economico', 'obra_id'], 'maquinarias_numero_economico_obra_unique');
        });

        // Tabla maquinaria_operadores (relación muchos a muchos)
        Schema::create('maquinaria_operador', function (Blueprint $table) {
            $table->id();
            $table->foreignId('maquinaria_id')->constrained('maquinarias')->onDelete('cascade');
            $table->foreignId('operador_id')->constrained('operadores')->onDelete('cascade');
        });

        // Tabla operador_tipo_maquinaria (relación muchos a muchos)
        Schema::create('operador_tipo_maquinaria', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operador_id')->constrained('operadores')->onDelete('cascade');
            $table->foreignId('tipo_maquinaria_id')->constrained('tipos_maquinaria')->onDelete('cascade');
        });

        // Tabla horómetros
        Schema::create('horometros', function (Blueprint $table) {
            $table->id();
            $table->foreignId('maquinaria_id')->constrained('maquinarias')->onDelete('cascade');
            $table->decimal('horometro_inicial');
            $table->decimal('horometro_final')->nullable();
            $table->boolean('parcialidad_turno')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('horometros');
        Schema::dropIfExists('maquinaria_operador');
        Schema::dropIfExists('operador_tipo_maquinaria');
        Schema::dropIfExists('operadores');
        Schema::dropIfExists('maquinaria');
        Schema::dropIfExists('tipo_maquinaria');
    }
};
