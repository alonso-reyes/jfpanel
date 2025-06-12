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
        Schema::create('zona_trabajo_dibujos', function (Blueprint $table) {
            $table->id();

            // Relación con el reporte
            $table->unsignedBigInteger('reporte_id');
            $table->foreign('reporte_id')
                ->references('id')
                ->on('reportes_jefe_frente')
                ->onDelete('cascade');

            // Relación con la zona de trabajo
            $table->unsignedBigInteger('zona_trabajo_id');
            $table->foreign('zona_trabajo_id')
                ->references('id')
                ->on('zonas_trabajo')
                ->onDelete('cascade');

            // Datos del dibujo
            $table->json('puntos')->nullable();
            $table->string('color')->default('4294198070')->nullable();
            $table->float('grosor')->default(4.0)->nullable();

            // Timestamps
            $table->timestamps();

            // Índices
            $table->index('reporte_id');
            $table->index('zona_trabajo_id');
            $table->string('ruta_imagen')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('zona_trabajo_dibujos');
    }
};
