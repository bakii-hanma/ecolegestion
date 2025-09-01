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
        Schema::create('student_grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->foreignId('class_id')->nullable()->constrained('classes')->onDelete('cascade');
            $table->foreignId('academic_year_id')->constrained('academic_years')->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained('teachers')->onDelete('cascade');
            $table->enum('term', ['1er trimestre', '2ème trimestre', '3ème trimestre']);
            $table->decimal('score', 5, 2); // Note obtenue
            $table->decimal('max_score', 5, 2)->default(20.00); // Note maximale (par défaut 20)
            $table->text('comments')->nullable(); // Commentaires du professeur
            $table->timestamps();

            // Index pour optimiser les requêtes
            $table->index(['student_id', 'subject_id', 'term', 'academic_year_id']);
            $table->index(['class_id', 'term', 'academic_year_id']);
            
            // Contrainte unique pour éviter les doublons
            $table->unique(['student_id', 'subject_id', 'term', 'academic_year_id'], 'unique_student_subject_term_year');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_grades');
    }
};
