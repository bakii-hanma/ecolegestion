<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Subject;
use App\Models\Level;

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $counter = 1;

        // Matières du préprimaire
        $preprimaireSubjects = [
            ['name' => 'Éveil', 'coefficient' => 1.0],
            ['name' => 'Activités Manuelles', 'coefficient' => 1.0],
            ['name' => 'Éducation Physique', 'coefficient' => 1.0],
            ['name' => 'Chant et Musique', 'coefficient' => 1.0],
        ];

        // Matières du primaire
        $primaireSubjects = [
            ['name' => 'Français', 'coefficient' => 4.0],
            ['name' => 'Mathématiques', 'coefficient' => 4.0],
            ['name' => 'Histoire-Géographie', 'coefficient' => 2.0],
            ['name' => 'Sciences', 'coefficient' => 2.0],
            ['name' => 'Éducation Civique', 'coefficient' => 1.0],
            ['name' => 'Éducation Physique et Sportive', 'coefficient' => 1.0],
            ['name' => 'Arts Plastiques', 'coefficient' => 1.0],
            ['name' => 'Éducation Musicale', 'coefficient' => 1.0],
        ];

        // Matières du collège
        $collegeSubjects = [
            ['name' => 'Français', 'coefficient' => 4.0],
            ['name' => 'Mathématiques', 'coefficient' => 4.0],
            ['name' => 'Histoire-Géographie', 'coefficient' => 3.0],
            ['name' => 'Sciences et Vie de la Terre', 'coefficient' => 3.0],
            ['name' => 'Physique-Chimie', 'coefficient' => 3.0],
            ['name' => 'Anglais', 'coefficient' => 2.0],
            ['name' => 'Espagnol', 'coefficient' => 2.0],
            ['name' => 'Allemand', 'coefficient' => 2.0],
            ['name' => 'Éducation Physique et Sportive', 'coefficient' => 1.0],
            ['name' => 'Arts Plastiques', 'coefficient' => 1.0],
            ['name' => 'Éducation Musicale', 'coefficient' => 1.0],
            ['name' => 'Technologie', 'coefficient' => 1.0],
        ];

        // Matières spécifiques du collège
        $collegeSpecificSubjects = [
            ['name' => 'Latin', 'coefficient' => 1.0, 'levels' => ['6EME', '5EME', '4EME', '3EME']],
            ['name' => 'Grec', 'coefficient' => 1.0, 'levels' => ['4EME', '3EME']],
            ['name' => 'Découverte Professionnelle', 'coefficient' => 1.0, 'levels' => ['3EME']],
        ];

        // Créer les matières du préprimaire
        $preprimaireLevels = Level::getPreprimaireLevels();
        foreach ($preprimaireLevels as $level) {
            foreach ($preprimaireSubjects as $subjectData) {
                $code = 'SUBJ_' . $counter . '_' . $level->code;
                
                Subject::create([
                    'name' => $subjectData['name'],
                    'code' => $code,
                    'coefficient' => $subjectData['coefficient'],
                    'level_id' => $level->id,
                    'description' => $subjectData['name'] . ' - ' . $level->name,
                    'is_active' => true,
                ]);
                
                $counter++;
            }
        }

        // Créer les matières du primaire
        $primaireLevels = Level::getPrimaireLevels();
        foreach ($primaireLevels as $level) {
            foreach ($primaireSubjects as $subjectData) {
                $code = 'SUBJ_' . $counter . '_' . $level->code;
                
                Subject::create([
                    'name' => $subjectData['name'],
                    'code' => $code,
                    'coefficient' => $subjectData['coefficient'],
                    'level_id' => $level->id,
                    'description' => $subjectData['name'] . ' - ' . $level->name,
                    'is_active' => true,
                ]);
                
                $counter++;
            }
        }

        // Créer les matières du collège
        $collegeLevels = Level::getCollegeLevels();
        foreach ($collegeLevels as $level) {
            foreach ($collegeSubjects as $subjectData) {
                $code = 'SUBJ_' . $counter . '_' . $level->code;
                
                Subject::create([
                    'name' => $subjectData['name'],
                    'code' => $code,
                    'coefficient' => $subjectData['coefficient'],
                    'level_id' => $level->id,
                    'description' => $subjectData['name'] . ' - ' . $level->name,
                    'is_active' => true,
                ]);
                
                $counter++;
            }
        }

        // Créer les matières spécifiques du collège
        foreach ($collegeSpecificSubjects as $subjectData) {
            $levels = Level::whereIn('code', $subjectData['levels'])->get();
            
            foreach ($levels as $level) {
                $code = 'SUBJ_' . $counter . '_' . $level->code;
                
                Subject::create([
                    'name' => $subjectData['name'],
                    'code' => $code,
                    'coefficient' => $subjectData['coefficient'],
                    'level_id' => $level->id,
                    'description' => $subjectData['name'] . ' - ' . $level->name,
                    'is_active' => true,
                ]);
                
                $counter++;
            }
        }

        $this->command->info('Matières créées avec succès !');
        $this->command->info('Matières du préprimaire ajoutées (PS, MS, GS).');
        $this->command->info('Matières du primaire ajoutées (CP à CM2).');
        $this->command->info('Matières du collège ajoutées (6ème à 3ème).');
    }
}
