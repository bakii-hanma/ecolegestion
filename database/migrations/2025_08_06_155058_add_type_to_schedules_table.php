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
        Schema::table('schedules', function (Blueprint $table) {
            // Type de créneau : course (cours) ou break (pause)
            $table->enum('type', ['course', 'break'])->default('course')->after('academic_year_id');
            
            // Rendre subject_id et teacher_id nullable pour les pauses
            $table->foreignId('subject_id')->nullable()->change();
            $table->foreignId('teacher_id')->nullable()->change();
            
            // Titre pour les pauses (Récréation, Pause déjeuner, etc.)
            $table->string('title')->nullable()->after('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropColumn(['type', 'title']);
            
            // Remettre subject_id et teacher_id comme obligatoires
            $table->foreignId('subject_id')->nullable(false)->change();
            $table->foreignId('teacher_id')->nullable(false)->change();
        });
    }
};
