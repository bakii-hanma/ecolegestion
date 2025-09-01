<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Student;
use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\Enrollment;
use Illuminate\Support\Facades\DB;

class TestStudentCreation extends Command
{
    protected $signature = 'students:test-creation';
    protected $description = 'Test student creation functionality';

    public function handle()
    {
        $this->info('Test de création d\'un élève...');
        
        DB::beginTransaction();
        try {
            // Données de test
            $testData = [
                'first_name' => 'Test',
                'last_name' => 'Étudiant',
                'date_of_birth' => '2010-01-01',
                'gender' => 'male',
                'place_of_birth' => 'Libreville',
                'address' => '123 Rue Test',
                'enrollment_date' => now(),
                'status' => 'active',
                'student_id' => Student::generateStudentId()
            ];
            
            $this->info('Données de test: ' . json_encode($testData, JSON_PRETTY_PRINT));
            
            // Créer l'élève
            $student = Student::create($testData);
            $this->info("Élève créé avec l'ID: {$student->id}");
            $this->info("Matricule généré: {$student->student_id}");
            
            // Créer une inscription de test
            $currentYear = AcademicYear::where('status', 'active')->first();
            $defaultClass = SchoolClass::where('is_active', true)->first();
            
            if ($currentYear && $defaultClass) {
                $enrollment = Enrollment::create([
                    'student_id' => $student->id,
                    'academic_year_id' => $currentYear->id,
                    'class_id' => $defaultClass->id,
                    'enrollment_date' => now(),
                    'enrollment_status' => 'active',
                    'status' => 'active',
                    'is_new_enrollment' => true,
                ]);
                
                $this->info("Inscription créée avec l'ID: {$enrollment->id}");
                
                // Vérifier les relations
                $studentWithClass = Student::with(['enrollments.schoolClass.level'])->find($student->id);
                $activeEnrollment = $studentWithClass->enrollments->where('status', 'active')->first();
                
                if ($activeEnrollment) {
                    $this->info("Classe actuelle: " . $activeEnrollment->schoolClass->name);
                    $this->info("Niveau actuel: " . ($activeEnrollment->schoolClass->level->name ?? 'NULL'));
                } else {
                    $this->error("Aucune inscription active trouvée");
                }
            } else {
                $this->error('Impossible de créer une inscription - année ou classe manquante');
            }
            
            DB::rollback(); // Ne pas sauvegarder les données de test
            $this->info('Test terminé avec succès (données annulées)');
            
        } catch (\Exception $e) {
            DB::rollback();
            $this->error('Erreur durant le test: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
        }
    }
}