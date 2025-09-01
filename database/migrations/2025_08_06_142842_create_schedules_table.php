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
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained()->onDelete('cascade');
            $table->foreignId('academic_year_id')->constrained()->onDelete('cascade');
            
            // Jour de la semaine (1 = Lundi, 7 = Dimanche)
            $table->integer('day_of_week');
            
            // Heures de début et fin
            $table->time('start_time');
            $table->time('end_time');
            
            // Salle de classe (optionnel)
            $table->string('room')->nullable();
            
            // Notes/commentaires
            $table->text('notes')->nullable();
            
            // Statut (actif/inactif)
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
            
            // Index pour optimiser les requêtes
            $table->index(['class_id', 'day_of_week', 'start_time']);
            $table->index(['teacher_id', 'day_of_week', 'start_time']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
