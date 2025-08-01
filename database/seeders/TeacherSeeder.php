<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Teacher;
use App\Models\SchoolClass;

class TeacherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Enseignants pour le pré-primaire (généralistes)
        $preprimaireClasses = SchoolClass::where('level', 'preprimaire')->get();

        foreach ($preprimaireClasses as $class) {
            Teacher::create([
                'employee_id' => 'EMP' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT),
                'first_name' => fake()->firstName(),
                'last_name' => fake()->lastName(),
                'email' => fake()->unique()->safeEmail(),
                'phone' => fake()->phoneNumber(),
                'date_of_birth' => fake()->date('Y-m-d', '-25 years'),
                'gender' => fake()->randomElement(['male', 'female']),
                'address' => fake()->address(),
                'qualification' => 'Diplôme en éducation préscolaire',
                'specialization' => null,
                'cycle' => 'preprimaire',
                'teacher_type' => 'general',
                'assigned_class_id' => $class->id,
                'hire_date' => fake()->date('Y-m-d', '-2 years'),
                'salary' => rand(80000, 120000),
                'status' => 'active',
            ]);
        }

        // Enseignants pour le primaire (généralistes)
        $primaireClasses = SchoolClass::where('level', 'primaire')->get();

        foreach ($primaireClasses as $class) {
            Teacher::create([
                'employee_id' => 'EMP' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT),
                'first_name' => fake()->firstName(),
                'last_name' => fake()->lastName(),
                'email' => fake()->unique()->safeEmail(),
                'phone' => fake()->phoneNumber(),
                'date_of_birth' => fake()->date('Y-m-d', '-30 years'),
                'gender' => fake()->randomElement(['male', 'female']),
                'address' => fake()->address(),
                'qualification' => 'Licence en éducation primaire',
                'specialization' => null,
                'cycle' => 'primaire',
                'teacher_type' => 'general',
                'assigned_class_id' => $class->id,
                'hire_date' => fake()->date('Y-m-d', '-3 years'),
                'salary' => rand(90000, 140000),
                'status' => 'active',
            ]);
        }

        // Enseignants pour le collège (spécialisés)
        $collegeSubjects = ['Mathématiques', 'Français', 'Histoire-Géographie', 'Sciences', 'Anglais', 'EPS'];
        
        foreach ($collegeSubjects as $subject) {
            Teacher::create([
                'employee_id' => 'EMP' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT),
                'first_name' => fake()->firstName(),
                'last_name' => fake()->lastName(),
                'email' => fake()->unique()->safeEmail(),
                'phone' => fake()->phoneNumber(),
                'date_of_birth' => fake()->date('Y-m-d', '-35 years'),
                'gender' => fake()->randomElement(['male', 'female']),
                'address' => fake()->address(),
                'qualification' => 'Master en ' . $subject,
                'specialization' => $subject,
                'cycle' => 'college',
                'teacher_type' => 'specialized',
                'assigned_class_id' => null,
                'hire_date' => fake()->date('Y-m-d', '-4 years'),
                'salary' => rand(100000, 160000),
                'status' => 'active',
            ]);
        }

        // Enseignants pour le lycée (spécialisés) - inactifs pour l'instant
        $lyceeSubjects = ['Mathématiques', 'Physique-Chimie', 'SVT', 'Histoire-Géographie', 'Philosophie', 'Langues'];
        
        foreach ($lyceeSubjects as $subject) {
            Teacher::create([
                'employee_id' => 'EMP' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT),
                'first_name' => fake()->firstName(),
                'last_name' => fake()->lastName(),
                'email' => fake()->unique()->safeEmail(),
                'phone' => fake()->phoneNumber(),
                'date_of_birth' => fake()->date('Y-m-d', '-40 years'),
                'gender' => fake()->randomElement(['male', 'female']),
                'address' => fake()->address(),
                'qualification' => 'Master en ' . $subject,
                'specialization' => $subject,
                'cycle' => 'lycee',
                'teacher_type' => 'specialized',
                'assigned_class_id' => null,
                'hire_date' => fake()->date('Y-m-d', '-5 years'),
                'salary' => rand(120000, 180000),
                'status' => 'inactive', // Inactif car le lycée n'est pas encore ouvert
            ]);
        }

        $this->command->info('Enseignants créés avec succès !');
    }
}
