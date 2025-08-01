<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SchoolClass extends Model
{
    protected $table = 'classes';
    
    protected $fillable = [
        'name',
        'level',
        'description',
        'capacity',
        'is_active',
        'level_id'
    ];
    
    protected $casts = [
        'is_active' => 'boolean'
    ];

    // Relation avec le niveau
    public function level(): BelongsTo
    {
        return $this->belongsTo(Level::class);
    }

    // Accesseur pour le nom complet avec niveau
    public function getFullNameAttribute()
    {
        $levelName = $this->level ? $this->level->name : '';
        return $this->name . ' - ' . $levelName;
    }

    // Scope pour les classes actives
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

    /**
     * Obtenir le niveau de façon sûre (objet Level ou null)
     */
    public function getSafeLevel()
    {
        // Si level_id existe et qu'on a une relation, retourner la relation
        if ($this->level_id && $this->relationLoaded('level') && $this->level instanceof Level) {
            return $this->level;
        }
        
        // Si level_id existe mais pas de relation chargée, charger le niveau
        if ($this->level_id) {
            return Level::find($this->level_id);
        }
        
        return null;
    }

    /**
     * Obtenir le nom du niveau de façon sûre
     */
    public function getSafeLevelName()
    {
        $safeLevel = $this->getSafeLevel();
        return $safeLevel ? $safeLevel->name : 'Non défini';
    }

    /**
     * Obtenir le cycle de façon sûre
     */
    public function getSafeCycle()
    {
        $safeLevel = $this->getSafeLevel();
        if ($safeLevel) {
            return $safeLevel->cycle;
        }
        
        // Fallback sur l'ancienne colonne level si elle existe
        if ($this->level && is_string($this->level)) {
            return $this->level;
        }
        
        return 'non-defini';
    }
}
