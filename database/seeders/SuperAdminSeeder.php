<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer le superadmin principal
        User::updateOrCreate(
            ['email' => 'superadmin@studiagabon.com'],
            [
                'name' => 'Super Administrateur',
                'email' => 'superadmin@studiagabon.com',
                'password' => Hash::make('SuperAdmin2025!'),
                'role' => 'superadmin',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // Créer un admin secondaire
        User::updateOrCreate(
            ['email' => 'admin@studiagabon.com'],
            [
                'name' => 'Administrateur Principal',
                'email' => 'admin@studiagabon.com',
                'password' => Hash::make('Admin2025!'),
                'role' => 'admin',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // Mettre à jour l'utilisateur existant s'il existe
        $existingUser = User::where('email', 'admin@example.com')->first();
        if ($existingUser) {
            $existingUser->update([
                'role' => 'admin',
                'is_active' => true,
            ]);
        }

        $this->command->info('Utilisateurs administrateurs créés avec succès !');
        $this->command->info('SuperAdmin: superadmin@studiagabon.com / SuperAdmin2025!');
        $this->command->info('Admin: admin@studiagabon.com / Admin2025!');
    }
}