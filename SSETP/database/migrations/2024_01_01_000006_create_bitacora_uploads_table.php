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
        Schema::create('bitacora_uploads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('etapa_productiva_id')->constrained('etapa_productivas')->onDelete('cascade');
            $table->integer('numero_bitacora'); // 1..12
            $table->enum('momento', ['1', '2', '3']);
            $table->string('file_path');
            $table->string('file_name');
            $table->enum('estado_revision', ['pendiente', 'aceptado', 'devuelto'])->default('pendiente');
            $table->text('observaciones_revision')->nullable();
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            // UNIQUE INDEX para evitar duplicados
            $table->unique(['etapa_productiva_id', 'numero_bitacora'], 'unique_bitacora_per_aprendiz');
            
            // Índices adicionales
            $table->index('estado_revision');
            $table->index(['etapa_productiva_id', 'momento']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bitacora_uploads');
    }
};