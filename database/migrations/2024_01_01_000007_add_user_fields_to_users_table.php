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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('instructor_id')->nullable()->constrained('instructores')->onDelete('set null');
            $table->foreignId('etapa_productiva_id')->nullable()->constrained('etapa_productivas')->onDelete('set null');
            $table->string('tipo_usuario')->default('admin'); // admin, instructor, aprendiz
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['instructor_id']);
            $table->dropForeign(['etapa_productiva_id']);
            $table->dropColumn(['instructor_id', 'etapa_productiva_id', 'tipo_usuario']);
        });
    }
};