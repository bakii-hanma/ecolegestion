<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'enrollment_id',
        'amount',
        'payment_date',
        'payment_method',
        'reference',
        'status',
        'notes'
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2'
    ];

    /**
     * Relation avec l'inscription
     */
    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    /**
     * Relation avec l'étudiant (via l'inscription)
     */
    public function student()
    {
        return $this->hasOneThrough(
            Student::class,
            Enrollment::class,
            'id', // Clé étrangère sur enrollments
            'id', // Clé primaire sur students
            'enrollment_id', // Clé étrangère sur payments
            'student_id' // Clé étrangère sur enrollments
        );
    }

    /**
     * Relation avec la classe (via l'inscription)
     */
    public function schoolClass()
    {
        return $this->hasOneThrough(
            SchoolClass::class,
            Enrollment::class,
            'id', // Clé étrangère sur enrollments
            'id', // Clé primaire sur classes
            'enrollment_id', // Clé étrangère sur payments
            'class_id' // Clé étrangère sur enrollments
        );
    }

    /**
     * Relation avec l'année académique (via l'inscription)
     */
    public function academicYear()
    {
        return $this->hasOneThrough(
            AcademicYear::class,
            Enrollment::class,
            'id', // Clé étrangère sur enrollments
            'id', // Clé primaire sur academic_years
            'enrollment_id', // Clé étrangère sur payments
            'academic_year_id' // Clé étrangère sur enrollments
        );
    }
}
