<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    protected $fillable = [
        'name',
        'code',
        'description',
        'cycle',
        'series',
        'coefficient',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'coefficient' => 'decimal:2',
        'series' => 'array'
    ];

    // Relation avec les notes
    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class);
    }

    // Relation avec les horaires
    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    // Relation many-to-many avec les enseignants
    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(Teacher::class, 'subject_teacher')
                    ->withTimestamps();
    }

    // Accesseur pour le nom complet avec cycle
    public function getFullNameAttribute()
    {
        return $this->name . ' (' . ucfirst($this->cycle) . ')';
    }

    // Scope pour les matières actives
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope pour filtrer par cycle
    public function scopeByCycle($query, $cycle)
    {
        return $query->where('cycle', $cycle);
    }

    // Scope pour filtrer par série (pour le lycée)
    public function scopeBySeries($query, $series)
    {
        return $query->whereJsonContains('series', $series);
    }

    // Méthode pour vérifier si une matière est applicable à une série donnée
    public function isApplicableToSeries($series)
    {
        if ($this->cycle !== 'lycee') {
            return true; // Les matières non-lycée s'appliquent partout
        }
        
        return in_array($series, $this->series ?? []);
    }



    // Méthode pour obtenir les enseignants actifs qui peuvent enseigner cette matière
    public function getAvailableTeachers()
    {
        return $this->teachers()->active()->get();
    }
}
