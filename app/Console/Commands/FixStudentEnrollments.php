<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Student;
use App\Models\Enrollment;
use App\Models\AcademicYear;
use App\Models\SchoolClass;

class FixStudentEnrollments extends Command
{
    protected $signature = 'students:fix-enrollments';
    protected $description = 'Fix students without active enrollments';

    public function handle()
    {
        $this->info('Vérification des étudiants sans inscription active...');
        
        // Étudiants sans inscription active
        $studentsWithoutEnrollment = Student::whereDoesntHave('enrollments', function($q) {
            $q->where('status', 'active');
        })->get();
        
        $this->info("Trouvé {$studentsWithoutEnrollment->count()} étudiants sans inscription active");
        
        if ($studentsWithoutEnrollment->count() === 0) {
            $this->info('Tous les étudiants ont une inscription active.');
            return;
        }
        
        // Récupérer l'année académique actuelle
        $currentYear = AcademicYear::where('is_current', true)->first();
        if (!$currentYear) {
            $currentYear = AcademicYear::first();
        }
        
        if (!$currentYear) {
            $this->error('Aucune année académique trouvée!');
            return;
        }
        
        // Récupérer une classe par défaut
        $defaultClass = SchoolClass::where('is_active', true)->first();
        if (!$defaultClass) {
            $this->error('Aucune classe active trouvée!');
            return;
        }
        
        $this->info("Création d'inscriptions dans la classe: {$defaultClass->name}");
        
        foreach ($studentsWithoutEnrollment as $student) {
            // Créer une inscription active
            Enrollment::create([
                'student_id' => $student->id,
                'academic_year_id' => $currentYear->id,
                'class_id' => $defaultClass->id,
                'enrollment_date' => $student->enrollment_date ?? now(),
                'enrollment_status' => 'enrolled',
                'status' => 'active',
                'is_new_enrollment' => false,
            ]);
            
            $this->info("Inscription créée pour: {$student->full_name}");
        }
        
        $this->info('Terminé!');
    }
}