<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class GenerateMatriculesSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        $this->command->info('Génération des matricules pour les utilisateurs existants...');
        
        // Récupérer tous les utilisateurs sans matricule
        $usersWithoutMatricule = User::whereNull('matricule')->get();
        
        $this->command->info("Trouvé {$usersWithoutMatricule->count()} utilisateur(s) sans matricule.");
        
        foreach ($usersWithoutMatricule as $user) {
            $matricule = User::generateMatricule($user->name);
            $user->update(['matricule' => $matricule]);
            
            $this->command->line("✓ Matricule '{$matricule}' généré pour {$user->name}");
        }
        
        $this->command->info('✅ Génération des matricules terminée !');
    }
}