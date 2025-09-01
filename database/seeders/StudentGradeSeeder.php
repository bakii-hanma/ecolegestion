<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\StudentGrade;
use App\Models\Student;
use App\Models\Subject;
use App\Models\SchoolClass;
use App\Models\Teacher;
use App\Models\AcademicYear;

class StudentGradeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer les données nécessaires
        $academicYear = AcademicYear::where('is_current', true)->first();
        if (!$academicYear) {
            $this->command->error('Aucune année académique active trouvée. Créez d\'abord une année académique.');
            return;
        }

        $students = Student::with(['enrollments.schoolClass'])->take(5)->get();
        $subjects = Subject::take(6)->get();
        $teachers = Teacher::take(3)->get();

        if ($students->isEmpty() || $subjects->isEmpty() || $teachers->isEmpty()) {
            $this->command->error('Données insuffisantes. Assurez-vous d\'avoir des élèves, matières et professeurs.');
            return;
        }

        $terms = ['1er trimestre', '2ème trimestre', '3ème trimestre'];

        foreach ($students as $student) {
            $currentEnrollment = $student->enrollments()->where('status', 'active')->first();
            if (!$currentEnrollment || !$currentEnrollment->schoolClass) {
                continue;
            }

            // Créer des notes pour chaque matière
            foreach ($subjects as $subject) {
                $teacher = $teachers->random();
                
                // Créer une note pour chaque trimestre
                foreach ($terms as $term) {
                    // Vérifier si la note existe déjà
                    $existingGrade = StudentGrade::where([
                        'student_id' => $student->id,
                        'subject_id' => $subject->id,
                        'term' => $term,
                        'academic_year_id' => $academicYear->id
                    ])->first();
                    
                    if (!$existingGrade) {
                        StudentGrade::create([
                            'student_id' => $student->id,
                            'subject_id' => $subject->id,
                            'class_id' => $currentEnrollment->class_id,
                            'academic_year_id' => $academicYear->id,
                            'teacher_id' => $teacher->id,
                            'term' => $term,
                            'score' => rand(8, 20), // Note entre 8 et 20
                            'max_score' => 20,
                            'comments' => 'Note de test générée automatiquement'
                        ]);
                    }
                }
            }
        }

        $this->command->info('Notes de test créées avec succès !');
    }
}
