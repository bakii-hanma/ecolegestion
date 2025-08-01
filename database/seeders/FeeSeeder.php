<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Fee;
use App\Models\Level;
use App\Models\SchoolClass;
use App\Models\AcademicYear;

class FeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $academicYear = AcademicYear::where('is_current', true)->first();
        if (!$academicYear) {
            $academicYear = AcademicYear::first();
        }

        $levels = Level::where('is_active', true)->get();

        foreach ($levels as $level) {
            // Définir les montants selon le cycle
            $tuitionAmount = match($level->cycle) {
                'preprimaire' => 120000,
                'primaire' => 180000,
                'college' => 250000,
                'lycee' => 350000,
                default => 200000
            };

            $registrationAmount = match($level->cycle) {
                'preprimaire' => 15000,
                'primaire' => 20000,
                'college' => 30000,
                'lycee' => 40000,
                default => 25000
            };

            // Frais de scolarité (pour tout le niveau, donc class_id = null)
            Fee::create([
                'name' => 'Frais de scolarité ' . $level->name,
                'description' => 'Frais de scolarité mensuel pour le niveau ' . $level->name,
                'amount' => $tuitionAmount,
                'class_id' => null, // Applicable à toutes les classes du niveau
                'academic_year_id' => $academicYear->id,
                'fee_type' => 'tuition',
                'frequency' => 'monthly',
                'is_active' => true,
                'is_mandatory' => true,
            ]);

            // Frais d'inscription (unique par année)
            Fee::create([
                'name' => 'Frais d\'inscription ' . $level->name,
                'description' => 'Frais d\'inscription annuel pour le niveau ' . $level->name,
                'amount' => $registrationAmount,
                'class_id' => null,
                'academic_year_id' => $academicYear->id,
                'fee_type' => 'registration',
                'frequency' => 'one_time',
                'is_active' => true,
                'is_mandatory' => true,
                'due_date' => $academicYear->start_date ?? now(),
            ]);
        }

        // Frais communs (uniformes, transport, etc.)
        $commonFees = [
            [
                'name' => 'Frais d\'uniforme',
                'description' => 'Achat obligatoire de l\'uniforme scolaire',
                'amount' => 35000,
                'fee_type' => 'uniform',
                'frequency' => 'yearly',
                'is_mandatory' => true,
            ],
            [
                'name' => 'Frais de transport',
                'description' => 'Transport scolaire (optionnel)',
                'amount' => 25000,
                'fee_type' => 'transport',
                'frequency' => 'monthly',
                'is_mandatory' => false,
            ],
            [
                'name' => 'Frais de cantine',
                'description' => 'Repas à la cantine (optionnel)',
                'amount' => 15000,
                'fee_type' => 'meal',
                'frequency' => 'monthly',
                'is_mandatory' => false,
            ],
        ];

        foreach ($commonFees as $feeData) {
            Fee::create(array_merge($feeData, [
                'class_id' => null,
                'academic_year_id' => $academicYear->id,
                'is_active' => true,
            ]));
        }

        $this->command->info('Frais de scolarité créés avec succès !');
        $this->command->info('Frais par niveau : Préprimaire (120k), Primaire (180k), Collège (250k)');
        $this->command->info('Frais communs : Uniforme, Transport, Cantine');
    }
}
