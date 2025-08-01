<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer un utilisateur administrateur par défaut
        User::create([
            'name' => 'Administrateur',
            'email' => 'admin@studiagabon.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);

        // Créer un utilisateur de test
        User::create([
            'name' => 'Utilisateur Test',
            'email' => 'test@studiagabon.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);

        $this->command->info('Utilisateurs créés avec succès !');
        $this->command->info('Email: admin@studiagabon.com | Mot de passe: password123');
        $this->command->info('Email: test@studiagabon.com | Mot de passe: password123');
    }
}
