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
        // Index pour améliorer les performances des requêtes fréquentes
        
        // Students table
        Schema::table('students', function (Blueprint $table) {
            $table->index('status', 'idx_students_status');
            $table->index('student_id', 'idx_students_student_id');
        });
        
        // Enrollments table
        Schema::table('enrollments', function (Blueprint $table) {
            $table->index('status', 'idx_enrollments_status');
            $table->index(['student_id', 'status'], 'idx_enrollments_student_status');
            $table->index(['class_id', 'status'], 'idx_enrollments_class_status');
            $table->index(['academic_year_id', 'status'], 'idx_enrollments_year_status');
        });
        
        // Classes table
        Schema::table('classes', function (Blueprint $table) {
            $table->index('is_active', 'idx_classes_is_active');
            $table->index(['level_id', 'is_active'], 'idx_classes_level_active');
        });
        
        // Academic years table
        Schema::table('academic_years', function (Blueprint $table) {
            $table->index('status', 'idx_academic_years_status');
            $table->index('is_current', 'idx_academic_years_is_current');
        });
        
        // Sessions table (pour améliorer les performances de connexion)
        Schema::table('sessions', function (Blueprint $table) {
            $table->index('last_activity', 'idx_sessions_last_activity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropIndex('idx_students_status');
            $table->dropIndex('idx_students_student_id');
        });
        
        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropIndex('idx_enrollments_status');
            $table->dropIndex('idx_enrollments_student_status');
            $table->dropIndex('idx_enrollments_class_status');
            $table->dropIndex('idx_enrollments_year_status');
        });
        
        Schema::table('classes', function (Blueprint $table) {
            $table->dropIndex('idx_classes_is_active');
            $table->dropIndex('idx_classes_level_active');
        });
        
        Schema::table('academic_years', function (Blueprint $table) {
            $table->dropIndex('idx_academic_years_status');
            $table->dropIndex('idx_academic_years_is_current');
        });
        
        Schema::table('sessions', function (Blueprint $table) {
            $table->dropIndex('idx_sessions_last_activity');
        });
    }
};