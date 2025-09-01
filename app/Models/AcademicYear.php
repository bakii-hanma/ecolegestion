<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademicYear extends Model
{
    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'is_current',
        'status',
        'description'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_current' => 'boolean'
    ];

    // Relations
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeCurrent($query)
    {
        return $query->where('is_current', true);
    }

    // Accesseurs
    public function getIsActiveAttribute()
    {
        return $this->status === 'active';
    }

    public function getFormattedPeriodAttribute()
    {
        return $this->start_date->format('d/m/Y') . ' - ' . $this->end_date->format('d/m/Y');
    }

    // Méthodes utiles
    public function makeCurrent()
    {
        // Désactiver toutes les autres années courantes
        static::where('is_current', true)->update(['is_current' => false]);
        
        // Activer cette année
        $this->update(['is_current' => true]);
    }

    public function getDurationInDays()
    {
        return $this->start_date->diffInDays($this->end_date);
    }

    public function isInProgress()
    {
        $now = now();
        return $now->greaterThanOrEqualTo($this->start_date) && 
               $now->lessThanOrEqualTo($this->end_date);
    }
}
