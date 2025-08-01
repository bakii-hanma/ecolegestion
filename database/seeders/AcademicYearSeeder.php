<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AcademicYear;

class AcademicYearSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $years = [
            [
                'name' => '2024-2025',
                'start_date' => '2024-09-01',
                'end_date' => '2025-07-31',
                'is_current' => true,
                'status' => 'active',
                'description' => 'Année scolaire 2024-2025'
            ],
            [
                'name' => '2023-2024',
                'start_date' => '2023-09-01',
                'end_date' => '2024-07-31',
                'is_current' => false,
                'status' => 'inactive',
                'description' => 'Année scolaire 2023-2024'
            ],
            [
                'name' => '2025-2026',
                'start_date' => '2025-09-01',
                'end_date' => '2026-07-31',
                'is_current' => false,
                'status' => 'inactive',
                'description' => 'Année scolaire 2025-2026'
            ]
        ];

        foreach ($years as $year) {
            AcademicYear::create($year);
        }

        $this->command->info('Années académiques créées avec succès !');
    }
}
