<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    protected $fillable = [
        'student_id',
        'first_name',
        'last_name',
        'date_of_birth',
        'gender',
        'place_of_birth',
        'address',
        'emergency_contact',
        'medical_conditions',
        'photo',
        'enrollment_date',
        'status'
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'enrollment_date' => 'date',
    ];

    /**
     * Générer automatiquement un matricule étudiant
     */
    public static function generateStudentId()
    {
        $year = date('Y');
        
        // Format: STU + YYYY + 4 chiffres
        $lastStudent = static::where('student_id', 'like', "STU{$year}%")
                            ->orderBy('student_id', 'desc')
                            ->first();
        
        $nextNumber = 1;
        if ($lastStudent) {
            $lastNumber = intval(substr($lastStudent->student_id, -4));
            $nextNumber = $lastNumber + 1;
        }
        
        return "STU{$year}" . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    // Relation avec les parents (many-to-many)
    public function parents(): BelongsToMany
    {
        return $this->belongsToMany(ParentModel::class, 'student_parent', 'student_id', 'parent_id');
    }

    // Relation avec les inscriptions
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    // Relation avec les notes
    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class);
    }

    // Relation avec les présences
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    // Relation avec les paiements
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    // Accesseur pour le nom complet
    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    // Accesseur pour l'âge
    public function getAgeAttribute()
    {
        return $this->date_of_birth ? $this->date_of_birth->age : null;
    }

    // Méthode pour obtenir la classe actuelle de manière sécurisée
    public function getCurrentClass()
    {
        $enrollment = $this->enrollments()
                          ->with('schoolClass.level')
                          ->where('status', 'active')
                          ->first();
        return $enrollment ? $enrollment->schoolClass : null;
    }

    // Méthode pour obtenir le niveau actuel de manière sécurisée
    public function getCurrentLevel()
    {
        $class = $this->getCurrentClass();
        if ($class && $class->level) {
            // Si level est une relation (objet), on le retourne
            if (is_object($class->level) && method_exists($class->level, 'name')) {
                return $class->level;
            }
            // Si level est une string, on crée un objet simple
            if (is_string($class->level)) {
                return (object) ['name' => $class->level];
            }
        }
        return null;
    }

    // Scope pour les élèves actifs
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
