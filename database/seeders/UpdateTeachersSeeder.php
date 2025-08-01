<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Teacher;

class UpdateTeachersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Mettre à jour tous les enseignants existants avec des valeurs par défaut
        Teacher::query()->update([
            'cycle' => 'college', // Par défaut, on met college
            'teacher_type' => 'specialized', // Par défaut, spécialisé
        ]);

        $this->command->info('Enseignants mis à jour avec succès !');
    }
}
