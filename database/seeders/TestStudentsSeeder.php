<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Student;
use App\Models\ParentModel;
use App\Models\Enrollment;
use App\Models\AcademicYear;
use App\Models\SchoolClass;

class TestStudentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer quelques élèves de test pour la réinscription
        $academicYear = AcademicYear::first();
        $class = SchoolClass::first();
        
        if (!$academicYear || !$class) {
            $this->command->warn('Aucune année scolaire ou classe trouvée. Créez d\'abord les données de base.');
            return;
        }

        $studentsData = [
            [
                'first_name' => 'Jean',
                'last_name' => 'DUPONT',
                'date_of_birth' => '2010-05-15',
                'gender' => 'male',
                'parent_data' => [
                    'first_name' => 'Pierre',
                    'last_name' => 'DUPONT',
                    'phone' => '+241 07 11 22 33',
                    'email' => 'pierre.dupont@email.com'
                ]
            ],
            [
                'first_name' => 'Marie',
                'last_name' => 'MARTIN',
                'date_of_birth' => '2011-08-20',
                'gender' => 'female',
                'parent_data' => [
                    'first_name' => 'Sophie',
                    'last_name' => 'MARTIN',
                    'phone' => '+241 07 44 55 66',
                    'email' => 'sophie.martin@email.com'
                ]
            ],
            [
                'first_name' => 'Paul',
                'last_name' => 'BERNARD',
                'date_of_birth' => '2009-12-03',
                'gender' => 'male',
                'parent_data' => [
                    'first_name' => 'Michel',
                    'last_name' => 'BERNARD',
                    'phone' => '+241 07 77 88 99',
                    'email' => 'michel.bernard@email.com'
                ]
            ]
        ];

        foreach ($studentsData as $data) {
            // Créer l'élève
            $student = Student::create([
                'student_id' => Student::generateStudentId(),
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'date_of_birth' => $data['date_of_birth'],
                'gender' => $data['gender'],
                'address' => 'Libreville, Gabon',
                'enrollment_date' => now()->subYear(),
                'status' => 'active'
            ]);

            // Créer ou récupérer le parent
            $parent = ParentModel::firstOrCreate(
                ['email' => $data['parent_data']['email']],
                [
                    'first_name' => $data['parent_data']['first_name'],
                    'last_name' => $data['parent_data']['last_name'],
                    'phone' => $data['parent_data']['phone'],
                    'address' => 'Libreville, Gabon',
                    'is_primary_contact' => true
                ]
            );

            // Lier l'élève au parent
            $student->parents()->attach($parent->id, [
                'relationship_type' => 'father',
                'is_primary_contact' => true
            ]);

            // Créer une inscription de l'année précédente (pour tester la réinscription)
            Enrollment::create([
                'student_id' => $student->id,
                'class_id' => $class->id,
                'academic_year_id' => $academicYear->id,
                'enrollment_date' => now()->subYear(),
                'status' => 'active', // Inscription active de l'année précédente
                'enrollment_status' => 'active',
                'payment_status' => 'completed',
                'total_fees' => 50000,
                'amount_paid' => 50000,
                'is_new_enrollment' => false
            ]);

            $this->command->info("✓ Élève créé : {$student->full_name} (Matricule: {$student->student_id})");
        }

        $this->command->info('✅ Élèves de test créés avec succès pour les tests de réinscription !');
    }
}