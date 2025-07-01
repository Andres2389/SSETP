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
        Schema::create('etapa_productivas', function (Blueprint $table) {
                $table->id();
                $table->json('tipo_documento')->nullable();
                $table->string('numero_documento')->nullable();
                $table->string('nombre')->nullable();
                $table->string('apellidos')->nullable();
                $table->string('celular')->nullable();
                $table->string('correo')->nullable();
                $table->json('tipo_alternativa')->nullable();
                $table->string('estado_sofia')->nullable();
                $table->foreignId('fichas_id')->nullable()->constrained('fichas')->nullOnDelete();
                $table->string('estado_ficha')->nullable();
                $table->foreignId('instructores_id')->nullable()->constrained('instructores')->nullOnDelete();
                $table->string('programa_formacion')->nullable();
                $table->string('numero_radicado')->nullable();
                $table->json('numero_bitacoras')->nullable();
                $table->timestamp('fecha_asignacion')->nullable();
                $table->timestamp('fecha_inicio_ep')->nullable();
                $table->timestamp('fecha_fin_ep')->nullable();
                $table->timestamp('fecha_corte')->nullable();
                $table->timestamp('fecha_17_meses')->nullable();
                $table->timestamp('fecha_inicio_alternativa')->nullable();
                $table->timestamp('fecha_fin_alternativa')->nullable();
                $table->text('observaciones')->nullable();
                $table->text('juicios_evaluativos')->nullable();
                $table->json('momentos')->nullable();
                $table->string('paz_salvo')->nullable();
                $table->timestamps();
            });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('etapa_productivas');
    }
};
