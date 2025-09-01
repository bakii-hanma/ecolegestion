<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Teacher;
use App\Models\Subject;
use App\Models\SchoolClass;

class TeacherSubjectClassSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $teachers = Teacher::all();
        $subjects = Subject::all();
        $classes = SchoolClass::all();

        if ($teachers->isEmpty() || $subjects->isEmpty() || $classes->isEmpty()) {
            $this->command->error('Données manquantes pour créer les associations');
            return;
        }

        // Associer chaque professeur à quelques matières
        foreach ($teachers as $teacher) {
            // Sélectionner 1-2 matières aléatoires pour chaque professeur (selon disponibilité)
            $maxSubjects = min(rand(1, 2), $subjects->count());
            $randomSubjects = $subjects->random($maxSubjects);
            
            // Vérifier les associations existantes pour éviter les doublons
            $existingSubjectIds = $teacher->subjects()->pluck('subjects.id')->toArray();
            $newSubjectIds = array_diff($randomSubjects->pluck('id')->toArray(), $existingSubjectIds);
            
            if (!empty($newSubjectIds)) {
                $teacher->subjects()->attach($newSubjectIds);
            }
            
            // Associer le professeur à 1 classe (selon disponibilité)
            $maxClasses = min(1, $classes->count());
            $randomClasses = $classes->random($maxClasses);
            
            // Vérifier les associations existantes pour éviter les doublons
            $existingClassIds = $teacher->classes()->pluck('classes.id')->toArray();
            $newClassIds = array_diff($randomClasses->pluck('id')->toArray(), $existingClassIds);
            
            if (!empty($newClassIds)) {
                $teacher->classes()->attach($newClassIds, [
                    'role' => 'professeur principal'
                ]);
            }
        }

        $this->command->info('Associations professeur-matière-classe créées avec succès !');
        
        // Afficher quelques statistiques
        $this->command->info("Total professeurs: {$teachers->count()}");
        $this->command->info("Total matières: {$subjects->count()}");
        $this->command->info("Total classes: {$classes->count()}");
        
        // Afficher quelques associations créées
        $this->command->info("\nExemples d'associations créées:");
        foreach ($teachers->take(3) as $teacher) {
            $teacherSubjects = $teacher->subjects()->pluck('name')->toArray();
            $teacherClasses = $teacher->classes()->pluck('name')->toArray();
            
            $this->command->info("{$teacher->first_name} {$teacher->last_name}:");
            $this->command->info("  Matières: " . implode(', ', $teacherSubjects));
            $this->command->info("  Classes: " . implode(', ', $teacherClasses));
        }
    }
}
