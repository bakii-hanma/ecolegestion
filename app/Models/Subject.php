<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    protected $fillable = [
        'name',
        'code',
        'description',
        'coefficient',
        'is_active',
        'level_id'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'coefficient' => 'decimal:2'
    ];

    // Relation avec le niveau
    public function level(): BelongsTo
    {
        return $this->belongsTo(Level::class);
    }

    // Relation avec les notes
    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class);
    }

    // Accesseur pour le nom complet avec niveau
    public function getFullNameAttribute()
    {
        $levelName = $this->level ? $this->level->name : '';
        return $this->name . ' (' . $levelName . ')';
    }

    // Scope pour les matières actives
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope pour filtrer par niveau
    public function scopeByLevel($query, $levelId)
    {
        return $query->where('level_id', $levelId);
    }

    // Scope pour filtrer par cycle
    public function scopeByCycle($query, $cycle)
    {
        return $query->whereHas('level', function($q) use ($cycle) {
            $q->where('cycle', $cycle);
        });
    }
}
