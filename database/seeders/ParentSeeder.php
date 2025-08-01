<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ParentModel;

class ParentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 1; $i <= 50; $i++) {
            ParentModel::create([
                'first_name' => fake()->firstName(),
                'last_name' => fake()->lastName(),
                'email' => fake()->unique()->safeEmail(),
                'phone' => fake()->phoneNumber(),
                'phone_2' => fake()->phoneNumber(),
                'gender' => fake()->randomElement(['male', 'female']),
                'address' => fake()->address(),
                'profession' => fake()->jobTitle(),
                'workplace' => fake()->company(),
                'relationship' => fake()->randomElement(['father', 'mother', 'guardian']),
                'is_primary_contact' => fake()->boolean(),
                'can_pickup' => true,
            ]);
        }

        $this->command->info('Parents créés avec succès !');
    }
}
