<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SchoolSettings;

class SchoolSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer les paramètres par défaut de l'établissement
        SchoolSettings::create([
            'school_name' => 'Lycée XXXXX',
            'school_address' => 'Libreville, Gabon',
            'school_phone' => '06037499',
            'school_email' => 'contact@lycee-xxxxx.ga',
            'school_website' => 'https://lycee-xxxxx.ga',
            'school_bp' => 'BP: 6',
            'school_motto' => 'Excellence, Discipline, Réussite',
            'school_description' => 'Établissement d\'enseignement secondaire de qualité',
            'principal_name' => 'Directeur de l\'établissement',
            'principal_title' => 'Le Proviseur',
            'academic_year' => '2024-2025',
            'school_type' => 'Lycée',
            'school_level' => 'Secondaire',
            'country' => 'Gabon',
            'city' => 'Libreville',
            'timezone' => 'Africa/Libreville',
            'currency' => 'FCFA',
            'language' => 'fr',
            'is_active' => true,
        ]);
    }
}
