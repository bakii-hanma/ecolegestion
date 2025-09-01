<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Subject;
use App\Models\Level;

class LyceeSubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Matières communes du lycée (toutes les classes)
        $lyceeCommonSubjects = [
            ['name' => 'Français', 'coefficient' => 5.0],
            ['name' => 'Mathématiques', 'coefficient' => 4.0],
            ['name' => 'Histoire-Géographie', 'coefficient' => 4.0],
            ['name' => 'Sciences et Vie de la Terre', 'coefficient' => 3.0],
            ['name' => 'Physique-Chimie', 'coefficient' => 4.0],
            ['name' => 'Anglais LV1', 'coefficient' => 3.0],
            ['name' => 'Espagnol LV2', 'coefficient' => 2.0],
            ['name' => 'Allemand LV2', 'coefficient' => 2.0],
            ['name' => 'Éducation Physique et Sportive', 'coefficient' => 2.0],
            ['name' => 'Enseignement Moral et Civique', 'coefficient' => 1.0],
        ];

        // Matières spécifiques de seconde
        $secondeSpecificSubjects = [
            ['name' => 'Sciences Économiques et Sociales', 'coefficient' => 1.5, 'levels' => ['2NDE']],
            ['name' => 'Sciences Numériques et Technologie', 'coefficient' => 1.5, 'levels' => ['2NDE']],
            ['name' => 'Accompagnement Personnalisé', 'coefficient' => 1.0, 'levels' => ['2NDE']],
        ];

        // Matières de spécialité (1ère et Terminale)
        $specialitySubjects = [
            // Spécialités scientifiques
            ['name' => 'Mathématiques Spécialité', 'coefficient' => 6.0, 'levels' => ['1ERE', 'TERMINAL']],
            ['name' => 'Physique-Chimie Spécialité', 'coefficient' => 6.0, 'levels' => ['1ERE', 'TERMINAL']],
            ['name' => 'SVT Spécialité', 'coefficient' => 6.0, 'levels' => ['1ERE', 'TERMINAL']],
            ['name' => 'Sciences de l\'Ingénieur', 'coefficient' => 6.0, 'levels' => ['1ERE', 'TERMINAL']],
            ['name' => 'Numérique et Sciences Informatiques', 'coefficient' => 6.0, 'levels' => ['1ERE', 'TERMINAL']],
            
            // Spécialités littéraires
            ['name' => 'Humanités, Littérature et Philosophie', 'coefficient' => 6.0, 'levels' => ['1ERE', 'TERMINAL']],
            ['name' => 'Langues, Littératures et Civilisations Étrangères Anglais', 'coefficient' => 6.0, 'levels' => ['1ERE', 'TERMINAL']],
            ['name' => 'Langues, Littératures et Civilisations Étrangères Espagnol', 'coefficient' => 6.0, 'levels' => ['1ERE', 'TERMINAL']],
            ['name' => 'Histoire-Géographie, Géopolitique et Sciences Politiques', 'coefficient' => 6.0, 'levels' => ['1ERE', 'TERMINAL']],
            
            // Spécialités économiques et sociales
            ['name' => 'Sciences Économiques et Sociales Spécialité', 'coefficient' => 6.0, 'levels' => ['1ERE', 'TERMINAL']],
            ['name' => 'Droit et Grands Enjeux du Monde Contemporain', 'coefficient' => 6.0, 'levels' => ['TERMINAL']],
        ];

        // Matières spécifiques à la Terminale
        $terminalSpecificSubjects = [
            ['name' => 'Philosophie', 'coefficient' => 4.0, 'levels' => ['TERMINAL']],
            ['name' => 'Grand Oral', 'coefficient' => 10.0, 'levels' => ['TERMINAL']],
        ];

        $counter = 200; // Commencer à partir de 200 pour éviter les conflits

        // Créer les matières communes pour tous les niveaux du lycée
        $lyceeLevels = Level::getLyceeLevels();
        
        if ($lyceeLevels->isEmpty()) {
            $this->command->error('Aucun niveau de lycée trouvé. Assurez-vous que les niveaux du lycée sont activés.');
            return;
        }

        foreach ($lyceeLevels as $level) {
            foreach ($lyceeCommonSubjects as $subjectData) {
                $code = 'LYCEE_' . $counter . '_' . $level->code;
                
                Subject::create([
                    'name' => $subjectData['name'],
                    'code' => $code,
                    'coefficient' => $subjectData['coefficient'],
                    'level_id' => $level->id,
                    'description' => $subjectData['name'] . ' - ' . $level->name . ' (Lycée)',
                    'is_active' => true,
                ]);
                
                $counter++;
            }
        }

        // Créer les matières spécifiques de seconde
        foreach ($secondeSpecificSubjects as $subjectData) {
            $levels = Level::whereIn('code', $subjectData['levels'])->get();
            
            foreach ($levels as $level) {
                $code = 'LYCEE_' . $counter . '_' . $level->code;
                
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

        // Créer les matières de spécialité
        foreach ($specialitySubjects as $subjectData) {
            $levels = Level::whereIn('code', $subjectData['levels'])->get();
            
            foreach ($levels as $level) {
                $code = 'LYCEE_' . $counter . '_' . $level->code;
                
                Subject::create([
                    'name' => $subjectData['name'],
                    'code' => $code,
                    'coefficient' => $subjectData['coefficient'],
                    'level_id' => $level->id,
                    'description' => $subjectData['name'] . ' - ' . $level->name . ' (Spécialité)',
                    'is_active' => true,
                ]);
                
                $counter++;
            }
        }

        // Créer les matières spécifiques à la Terminale
        foreach ($terminalSpecificSubjects as $subjectData) {
            $levels = Level::whereIn('code', $subjectData['levels'])->get();
            
            foreach ($levels as $level) {
                $code = 'LYCEE_' . $counter . '_' . $level->code;
                
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

        $this->command->info('Matières du lycée créées avec succès !');
        $this->command->info('- Matières communes pour 2nde, 1ère, et Terminale');
        $this->command->info('- Matières spécifiques de 2nde (SES, SNT, AP)');
        $this->command->info('- Spécialités de 1ère et Terminale (sciences, littérature, économie)');
        $this->command->info('- Matières spécifiques à la Terminale (Philosophie, Grand Oral)');
        
        // Afficher le nombre de matières créées par niveau
        foreach ($lyceeLevels as $level) {
            $subjectCount = Subject::where('level_id', $level->id)->count();
            $this->command->info("- {$level->name} : {$subjectCount} matières");
        }
    }
}
