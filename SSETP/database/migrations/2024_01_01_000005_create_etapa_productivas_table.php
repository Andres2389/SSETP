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
            $table->foreignId('fichas_id')->nullable()->constrained('fichas')->nullOnDelete();
            $table->foreignId('instructores_id')->nullable()->constrained('instructores')->nullOnDelete();
            $table->string('tipo_documento')->nullable();
            $table->string('numero_documento')->nullable();
            $table->string('nombre')->nullable();
            $table->string('apellidos')->nullable();
            $table->string('celular')->nullable();
            $table->string('correo')->nullable();
            $table->enum('estado_sofia', ['aplazado', 'en_formacion', 'por_certificar', 'certificado', 'cancelado','trasladado','condicionado'])->nullable();
            $table->enum('estado_ficha', ['aplazado', 'en_formacion', 'por_certificar', 'certificado', 'cancelado','trasladado','condicionado'])->nullable();
            $table->date('fecha_inicio_ep')->nullable();
            $table->date('fecha_17_meses')->nullable();
            $table->date('fecha_asignacion')->nullable();
            $table->string('tipo_alternativa')->nullable();
            $table->date('fecha_inicio_alternativa')->nullable();
            $table->date('fecha_fin_alternativa')->nullable();
            $table->date('fecha_corte')->nullable();
            $table->text('observaciones')->nullable();
            $table->text('juicios_evaluativos')->nullable();
            $table->enum('momentos', ['1', '2', '3'])->nullable();
            $table->boolean('paz_salvo')->default(false);
            $table->timestamps();

            // Índices

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
