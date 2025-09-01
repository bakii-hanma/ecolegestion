<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Teacher;
use App\Models\Subject;

class TeacherSubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer tous les enseignants et matières
        $teachers = Teacher::all();
        $subjects = Subject::all();

        // Si nous avons des enseignants et des matières
        if ($teachers->count() > 0 && $subjects->count() > 0) {
            
            // Associer quelques enseignants à des matières selon leur spécialisation
            foreach ($teachers as $teacher) {
                // Pour les enseignants spécialisés, associer selon leur spécialisation
                if ($teacher->teacher_type === 'specialized' && !empty($teacher->specialization)) {
                    // Rechercher les matières qui correspondent à la spécialisation
                    $matchingSubjects = $subjects->filter(function($subject) use ($teacher) {
                        return stripos($subject->name, $teacher->specialization) !== false ||
                               stripos($teacher->specialization, $subject->name) !== false;
                    });
                    
                    if ($matchingSubjects->count() > 0) {
                        $teacher->subjects()->attach($matchingSubjects->pluck('id')->toArray());
                    } else {
                        // Si aucune correspondance exacte, associer à 1-2 matières aléatoires
                        $randomSubjects = $subjects->random(min(2, $subjects->count()));
                        $teacher->subjects()->attach($randomSubjects->pluck('id')->toArray());
                    }
                } else {
                    // Pour les enseignants généralistes, associer à plusieurs matières
                    $subjectCount = min(3, $subjects->count());
                    $randomSubjects = $subjects->random($subjectCount);
                    $teacher->subjects()->attach($randomSubjects->pluck('id')->toArray());
                }
            }
            
            $this->command->info('Associations enseignants-matières créées avec succès!');
        } else {
            $this->command->warn('Aucun enseignant ou matière trouvé pour créer les associations.');
        }
    }
}
