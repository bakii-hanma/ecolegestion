<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            // Permettre student_id d'être nullable pour les inscriptions en attente
            $table->unsignedBigInteger('student_id')->nullable()->change();
            
            // Ajouter les informations de base de l'inscrit (nullable au début)
            $table->string('applicant_first_name')->nullable()->after('student_id');
            $table->string('applicant_last_name')->nullable()->after('applicant_first_name');
            $table->date('applicant_date_of_birth')->nullable()->after('applicant_last_name');
            $table->enum('applicant_gender', ['male', 'female'])->nullable()->after('applicant_date_of_birth');
            $table->string('applicant_phone')->nullable()->after('applicant_gender');
            $table->string('applicant_email')->nullable()->after('applicant_phone');
            $table->text('applicant_address')->nullable()->after('applicant_email');
            
            // Informations du parent/tuteur responsable de l'inscription (nullable au début)
            $table->string('parent_first_name')->nullable()->after('applicant_address');
            $table->string('parent_last_name')->nullable()->after('parent_first_name');
            $table->string('parent_phone')->nullable()->after('parent_last_name');
            $table->string('parent_email')->nullable()->after('parent_phone');
            $table->enum('parent_relationship', ['father', 'mother', 'guardian', 'other'])->nullable()->after('parent_email');
            
            // Nouveau statut d'inscription
            $table->enum('enrollment_status', ['pending', 'student_created', 'active', 'inactive', 'transferred', 'graduated'])
                  ->default('student_created')
                  ->after('status');
                  
            // Flag pour savoir si c'est une nouvelle inscription ou une réinscription
            $table->boolean('is_new_enrollment')->default(false)->after('enrollment_status');
        });
        
        // Mettre à jour les données existantes
        DB::statement("UPDATE enrollments SET enrollment_status = 'student_created', is_new_enrollment = false WHERE student_id IS NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            // Remettre student_id comme obligatoire
            $table->unsignedBigInteger('student_id')->nullable(false)->change();
            
            // Supprimer les nouvelles colonnes
            $table->dropColumn([
                'applicant_first_name',
                'applicant_last_name', 
                'applicant_date_of_birth',
                'applicant_gender',
                'applicant_phone',
                'applicant_email',
                'applicant_address',
                'parent_first_name',
                'parent_last_name',
                'parent_phone',
                'parent_email',
                'parent_relationship',
                'enrollment_status',
                'is_new_enrollment'
            ]);
        });
    }
};
