<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Enrollment;
use App\Models\Student;
use App\Models\SchoolClass;
use App\Models\AcademicYear;

class EnrollmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $students = Student::all();
        $classes = SchoolClass::where('is_active', true)->get();
        $academicYear = AcademicYear::where('is_current', true)->first();

        foreach ($students as $student) {
            $class = $classes->random();
            
            Enrollment::create([
                'student_id' => $student->id,
                'class_id' => $class->id,
                'academic_year_id' => $academicYear->id,
                'enrollment_date' => fake()->date('Y-m-d', '-6 months'),
                'status' => 'active',
            ]);
        }

        $this->command->info('Inscriptions créées avec succès !');
    }
}
