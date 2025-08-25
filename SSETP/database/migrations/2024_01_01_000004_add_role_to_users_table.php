<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'instructor', 'aprendiz'])->default('aprendiz');
            $table->string('numero_documento')->nullable();
            //$table->foreignId('instructor_id')->nullable()->constrained('instructores')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'numero_documento', 'instructor_id']);
        });
    }
};
