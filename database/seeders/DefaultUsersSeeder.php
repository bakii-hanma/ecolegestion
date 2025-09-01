<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DefaultUsersSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        $this->command->info('Création des utilisateurs par défaut pour tous les rôles...');

        $defaultUsers = [
            // SuperAdmin
            [
                'name' => 'Super Administrateur StudiaGabon',
                'email' => 'superadmin@studiagabon.com',
                'password' => 'SuperAdmin2025!',
                'role' => 'superadmin',
                'is_active' => true,
            ],

            // Admin
            [
                'name' => 'Administrateur Principal',
                'email' => 'admin@studiagabon.com',
                'password' => 'Admin2025!',
                'role' => 'admin',
                'is_active' => true,
            ],

            // Enseignant 1
            [
                'name' => 'Marie Claire OBAMA',
                'email' => 'marie.obama@studiagabon.com',
                'password' => 'Marie1234',
                'role' => 'teacher',
                'is_active' => true,
            ],

            // Enseignant 2
            [
                'name' => 'Jean Baptiste MBALLA',
                'email' => 'jean.mballa@studiagabon.com',
                'password' => 'Jean1234',
                'role' => 'teacher',
                'is_active' => true,
            ],

            // Enseignant 3
            [
                'name' => 'Paul NLEND',
                'email' => 'paul.nlend@studiagabon.com',
                'password' => 'Paul1234',
                'role' => 'teacher',
                'is_active' => true,
            ],

            // Secrétaire 1
            [
                'name' => 'Sylvie NGONO',
                'email' => 'sylvie.ngono@studiagabon.com',
                'password' => 'Sylvie1234',
                'role' => 'secretary',
                'is_active' => true,
            ],

            // Secrétaire 2
            [
                'name' => 'Flore MENGUE',
                'email' => 'flore.mengue@studiagabon.com',
                'password' => 'Flore1234',
                'role' => 'secretary',
                'is_active' => true,
            ],
        ];

        foreach ($defaultUsers as $userData) {
            // Vérifier si l'utilisateur existe déjà
            $existingUser = User::where('email', $userData['email'])->first();
            
            if ($existingUser) {
                $this->command->warn("Utilisateur {$userData['email']} existe déjà - ignoré");
                continue;
            }

            // Générer le matricule
            $matricule = User::generateMatricule($userData['name']);

            // Créer l'utilisateur
            $user = User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => Hash::make($userData['password']),
                'matricule' => $matricule,
                'role' => $userData['role'],
                'is_active' => $userData['is_active'],
            ]);

            $this->command->line("✓ Utilisateur créé : {$userData['name']} ({$userData['role']}) - Matricule: {$matricule}");
        }

        $this->command->info('✅ Création des utilisateurs par défaut terminée !');
    }
}