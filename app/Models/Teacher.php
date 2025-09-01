<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Teacher extends Model
{
    protected $fillable = [
        'employee_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'date_of_birth',
        'gender',
        'address',
        'qualification',
        'specialization',
        'cycle',
        'teacher_type',
        'assigned_class_id',
        'hire_date',
        'salary',
        'status',
        'photo'
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'hire_date' => 'date',
        'salary' => 'decimal:2',
    ];

    /**
     * Générer automatiquement un matricule enseignant
     */
    public static function generateEmployeeId()
    {
        $year = date('Y');
        
        // Format: ENS + YYYY + 4 chiffres
        $lastTeacher = static::where('employee_id', 'like', "ENS{$year}%")
                            ->orderBy('employee_id', 'desc')
                            ->first();
        
        $nextNumber = 1;
        if ($lastTeacher) {
            $lastNumber = intval(substr($lastTeacher->employee_id, -4));
            $nextNumber = $lastNumber + 1;
        }
        
        return "ENS{$year}" . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    // Relation avec les notes attribuées
    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class);
    }

    // Relation avec les horaires
    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    // Relation avec la classe assignée (pour enseignants généralistes)
    public function assignedClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'assigned_class_id');
    }

    // Relation many-to-many avec les matières
    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'subject_teacher')
                    ->withTimestamps();
    }

    // Relation many-to-many avec les classes
    public function classes(): BelongsToMany
    {
        return $this->belongsToMany(SchoolClass::class, 'class_teacher', 'teacher_id', 'class_id')
                    ->withPivot('role')
                    ->withTimestamps();
    }

    // Accesseur pour le nom complet
    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    // Accesseur pour l'ancienneté
    public function getYearsOfServiceAttribute()
    {
        return $this->hire_date ? $this->hire_date->diffInYears(now()) : 0;
    }

    // Accesseur pour le type d'enseignant en français
    public function getTeacherTypeLabelAttribute()
    {
        return $this->teacher_type === 'general' ? 'Généraliste' : 'Spécialisé';
    }

    // Accesseur pour le cycle en français
    public function getCycleLabelAttribute()
    {
        $cycles = [
            'preprimaire' => 'Pré-primaire',
            'primaire' => 'Primaire',
            'college' => 'Collège',
            'lycee' => 'Lycée'
        ];
        return $cycles[$this->cycle] ?? $this->cycle;
    }

    // Scope pour les enseignants actifs
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // Scope pour filtrer par cycle
    public function scopeByCycle($query, $cycle)
    {
        return $query->where('cycle', $cycle);
    }

    // Scope pour filtrer par type d'enseignant
    public function scopeByType($query, $type)
    {
        return $query->where('teacher_type', $type);
    }

    // Scope pour les enseignants généralistes
    public function scopeGeneral($query)
    {
        return $query->where('teacher_type', 'general');
    }

    // Scope pour les enseignants spécialisés
    public function scopeSpecialized($query)
    {
        return $query->where('teacher_type', 'specialized');
    }



    // Méthode pour vérifier si l'enseignant peut enseigner une matière
    public function canTeach($subjectId)
    {
        return $this->subjects()->where('subject_id', $subjectId)->exists();
    }

    // Scope pour les enseignants qui peuvent enseigner une matière spécifique
    public function scopeCanTeachSubject($query, $subjectId)
    {
        return $query->whereHas('subjects', function($q) use ($subjectId) {
            $q->where('subject_id', $subjectId);
        });
    }
}
