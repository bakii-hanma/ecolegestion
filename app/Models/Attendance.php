<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    protected $fillable = [
        'student_id',
        'class_id',
        'attendance_date',
        'status',
        'arrival_time',
        'reason',
        'justified'
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'arrival_time' => 'datetime',
        'justified' => 'boolean'
    ];

    /**
     * Relation avec l'élève
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Relation avec la classe
     */
    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    /**
     * Accessor pour le statut formaté
     */
    public function getStatusFormattedAttribute(): string
    {
        return match($this->status) {
            'present' => 'Présent',
            'absent' => 'Absent',
            'late' => 'En retard',
            'excused' => 'Excusé',
            default => 'Inconnu'
        };
    }

    /**
     * Accessor pour la couleur du statut
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'present' => 'success',
            'absent' => 'danger',
            'late' => 'warning',
            'excused' => 'info',
            default => 'secondary'
        };
    }
}
