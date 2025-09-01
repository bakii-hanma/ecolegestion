<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Subject;

class GabonSubjectsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subjects = [
            // PRIMAIRE
            [
                'name' => 'Français',
                'code' => 'FR_PRIM',
                'description' => 'Langue française et littérature',
                'cycle' => 'primaire',
                'series' => null,
                'coefficient' => 3,
                'is_active' => true
            ],
            [
                'name' => 'Mathématiques',
                'code' => 'MATH_PRIM',
                'description' => 'Mathématiques générales',
                'cycle' => 'primaire',
                'series' => null,
                'coefficient' => 3,
                'is_active' => true
            ],
            [
                'name' => 'Sciences d\'observation',
                'code' => 'SCI_PRIM',
                'description' => 'Sciences et observation',
                'cycle' => 'primaire',
                'series' => null,
                'coefficient' => 2,
                'is_active' => true
            ],
            [
                'name' => 'Histoire-Géographie',
                'code' => 'HG_PRIM',
                'description' => 'Histoire et géographie',
                'cycle' => 'primaire',
                'series' => null,
                'coefficient' => 2,
                'is_active' => true
            ],
            [
                'name' => 'Instruction civique',
                'code' => 'IC_PRIM',
                'description' => 'Éducation civique et morale',
                'cycle' => 'primaire',
                'series' => null,
                'coefficient' => 1,
                'is_active' => true
            ],
            [
                'name' => 'Éducation physique et sportive',
                'code' => 'EPS_PRIM',
                'description' => 'Sport et éducation physique',
                'cycle' => 'primaire',
                'series' => null,
                'coefficient' => 1,
                'is_active' => true
            ],

            // COLLÈGE
            [
                'name' => 'Français',
                'code' => 'FR_COL',
                'description' => 'Langue française et littérature',
                'cycle' => 'college',
                'series' => null,
                'coefficient' => 4,
                'is_active' => true
            ],
            [
                'name' => 'Mathématiques',
                'code' => 'MATH_COL',
                'description' => 'Mathématiques générales',
                'cycle' => 'college',
                'series' => null,
                'coefficient' => 4,
                'is_active' => true
            ],
            [
                'name' => 'Anglais',
                'code' => 'ANG_COL',
                'description' => 'Langue anglaise',
                'cycle' => 'college',
                'series' => null,
                'coefficient' => 3,
                'is_active' => true
            ],
            [
                'name' => 'Sciences physiques',
                'code' => 'SP_COL',
                'description' => 'Physique et chimie',
                'cycle' => 'college',
                'series' => null,
                'coefficient' => 3,
                'is_active' => true
            ],
            [
                'name' => 'Sciences de la Vie et de la Terre',
                'code' => 'SVT_COL',
                'description' => 'Biologie et géologie',
                'cycle' => 'college',
                'series' => null,
                'coefficient' => 3,
                'is_active' => true
            ],
            [
                'name' => 'Histoire-Géographie',
                'code' => 'HG_COL',
                'description' => 'Histoire et géographie',
                'cycle' => 'college',
                'series' => null,
                'coefficient' => 3,
                'is_active' => true
            ],
            [
                'name' => 'Éducation civique',
                'code' => 'EC_COL',
                'description' => 'Instruction civique et morale',
                'cycle' => 'college',
                'series' => null,
                'coefficient' => 1,
                'is_active' => true
            ],
            [
                'name' => 'Éducation physique et sportive',
                'code' => 'EPS_COL',
                'description' => 'Sport et éducation physique',
                'cycle' => 'college',
                'series' => null,
                'coefficient' => 2,
                'is_active' => true
            ],
            [
                'name' => 'Arts plastiques',
                'code' => 'ART_COL',
                'description' => 'Dessin et arts visuels',
                'cycle' => 'college',
                'series' => null,
                'coefficient' => 1,
                'is_active' => true
            ],
            [
                'name' => 'Musique',
                'code' => 'MUS_COL',
                'description' => 'Éducation musicale',
                'cycle' => 'college',
                'series' => null,
                'coefficient' => 1,
                'is_active' => true
            ],
            [
                'name' => 'Technologie',
                'code' => 'TECH_COL',
                'description' => 'Éducation technologique',
                'cycle' => 'college',
                'series' => null,
                'coefficient' => 2,
                'is_active' => true
            ],

            // LYCÉE - MATIÈRES COMMUNES
            [
                'name' => 'Français',
                'code' => 'FR_LYC',
                'description' => 'Langue française et littérature',
                'cycle' => 'lycee',
                'series' => ['S', 'A1', 'A2', 'B', 'C', 'D', 'E'],
                'coefficient' => 3,
                'is_active' => true
            ],
            [
                'name' => 'Anglais',
                'code' => 'ANG_LYC',
                'description' => 'Langue anglaise',
                'cycle' => 'lycee',
                'series' => ['S', 'A1', 'A2', 'B', 'C', 'D', 'E'],
                'coefficient' => 2,
                'is_active' => true
            ],
            [
                'name' => 'Histoire-Géographie',
                'code' => 'HG_LYC',
                'description' => 'Histoire et géographie',
                'cycle' => 'lycee',
                'series' => ['A1', 'A2', 'B', 'S'],
                'coefficient' => 3,
                'is_active' => true
            ],
            [
                'name' => 'Éducation civique',
                'code' => 'EC_LYC',
                'description' => 'Instruction civique et morale',
                'cycle' => 'lycee',
                'series' => ['S', 'A1', 'A2', 'B', 'C', 'D', 'E'],
                'coefficient' => 1,
                'is_active' => true
            ],
            [
                'name' => 'Éducation physique et sportive',
                'code' => 'EPS_LYC',
                'description' => 'Sport et éducation physique',
                'cycle' => 'lycee',
                'series' => ['S', 'A1', 'A2', 'B', 'C', 'D', 'E'],
                'coefficient' => 1,
                'is_active' => true
            ],

            // LYCÉE - SÉRIE S (SCIENTIFIQUE)
            [
                'name' => 'Mathématiques',
                'code' => 'MATH_S',
                'description' => 'Mathématiques avancées',
                'cycle' => 'lycee',
                'series' => ['S', 'C', 'D'],
                'coefficient' => 6,
                'is_active' => true
            ],
            [
                'name' => 'Sciences physiques',
                'code' => 'SP_S',
                'description' => 'Physique et chimie',
                'cycle' => 'lycee',
                'series' => ['S', 'C', 'D'],
                'coefficient' => 5,
                'is_active' => true
            ],
            [
                'name' => 'Sciences de la Vie et de la Terre',
                'code' => 'SVT_S',
                'description' => 'Biologie et géologie',
                'cycle' => 'lycee',
                'series' => ['S', 'D'],
                'coefficient' => 4,
                'is_active' => true
            ],

            // LYCÉE - SÉRIES LITTÉRAIRES (A1, A2, B)
            [
                'name' => 'Philosophie',
                'code' => 'PHILO',
                'description' => 'Philosophie',
                'cycle' => 'lycee',
                'series' => ['A1', 'A2', 'B', 'S'],
                'coefficient' => 4,
                'is_active' => true
            ],
            [
                'name' => 'Littérature',
                'code' => 'LITT',
                'description' => 'Littérature française et africaine',
                'cycle' => 'lycee',
                'series' => ['A1', 'A2'],
                'coefficient' => 4,
                'is_active' => true
            ],
            [
                'name' => 'Latin',
                'code' => 'LAT',
                'description' => 'Langue latine',
                'cycle' => 'lycee',
                'series' => ['A1'],
                'coefficient' => 3,
                'is_active' => true
            ],
            [
                'name' => 'Grec',
                'code' => 'GREC',
                'description' => 'Langue grecque',
                'cycle' => 'lycee',
                'series' => ['A1'],
                'coefficient' => 3,
                'is_active' => true
            ],
            [
                'name' => 'Espagnol',
                'code' => 'ESP',
                'description' => 'Langue espagnole',
                'cycle' => 'lycee',
                'series' => ['A1', 'A2', 'B'],
                'coefficient' => 3,
                'is_active' => true
            ],

            // LYCÉE - SÉRIE B (ÉCONOMIQUE ET SOCIAL)
            [
                'name' => 'Sciences économiques et sociales',
                'code' => 'SES',
                'description' => 'Économie et sociologie',
                'cycle' => 'lycee',
                'series' => ['B'],
                'coefficient' => 5,
                'is_active' => true
            ],
            [
                'name' => 'Comptabilité',
                'code' => 'COMPTA',
                'description' => 'Comptabilité générale',
                'cycle' => 'lycee',
                'series' => ['B'],
                'coefficient' => 4,
                'is_active' => true
            ],
            [
                'name' => 'Mathématiques appliquées',
                'code' => 'MATH_B',
                'description' => 'Mathématiques pour l\'économie',
                'cycle' => 'lycee',
                'series' => ['B'],
                'coefficient' => 3,
                'is_active' => true
            ],

            // LYCÉE - SÉRIE C (MATHÉMATIQUES)
            [
                'name' => 'Mathématiques spécialisées',
                'code' => 'MATH_C',
                'description' => 'Mathématiques très avancées',
                'cycle' => 'lycee',
                'series' => ['C'],
                'coefficient' => 7,
                'is_active' => true
            ],

            // LYCÉE - SÉRIE D (SCIENCES NATURELLES)
            [
                'name' => 'Biologie approfondie',
                'code' => 'BIO_D',
                'description' => 'Biologie spécialisée',
                'cycle' => 'lycee',
                'series' => ['D'],
                'coefficient' => 6,
                'is_active' => true
            ],

            // LYCÉE - SÉRIE E (TECHNIQUE)
            [
                'name' => 'Technologie industrielle',
                'code' => 'TECH_IND',
                'description' => 'Technologies industrielles',
                'cycle' => 'lycee',
                'series' => ['E'],
                'coefficient' => 6,
                'is_active' => true
            ],
            [
                'name' => 'Dessin technique',
                'code' => 'DESS_TECH',
                'description' => 'Dessin technique et industriel',
                'cycle' => 'lycee',
                'series' => ['E'],
                'coefficient' => 4,
                'is_active' => true
            ]
        ];

        foreach ($subjects as $subject) {
            Subject::updateOrCreate(
                ['code' => $subject['code']],
                $subject
            );
        }
    }
}