<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Fee extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'amount',
        'fee_type',
        'frequency',
        'class_id',
        'academic_year_id',
        'due_date',
        'is_mandatory',
        'is_active'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'due_date' => 'date',
        'is_mandatory' => 'boolean',
        'is_active' => 'boolean'
    ];

    /**
     * Relation avec la classe
     */
    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    /**
     * Relation avec l'année académique
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    // Note: The payments relationship has been removed as payments are now linked to enrollments
    // To get payments for a fee, you need to go through enrollments and their associated fees

    /**
     * Scope pour les frais actifs
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope pour les frais obligatoires
     */
    public function scopeMandatory($query)
    {
        return $query->where('is_mandatory', true);
    }

    /**
     * Scope par type de frais
     */
    public function scopeByType($query, $type)
    {
        return $query->where('fee_type', $type);
    }

    /**
     * Scope par fréquence
     */
    public function scopeByFrequency($query, $frequency)
    {
        return $query->where('frequency', $frequency);
    }

    /**
     * Scope par classe
     */
    public function scopeByClass($query, $classId)
    {
        return $query->where('class_id', $classId);
    }

    /**
     * Scope par année académique
     */
    public function scopeByAcademicYear($query, $academicYearId)
    {
        return $query->where('academic_year_id', $academicYearId);
    }

    /**
     * Accesseur pour le montant formaté
     */
    public function getFormattedAmountAttribute()
    {
        return number_format($this->amount, 0, ',', ' ') . ' FCFA';
    }

    /**
     * Accesseur pour le type de frais en français
     */
    public function getFeeTypeLabelAttribute()
    {
        $types = [
            'tuition' => 'Scolarité',
            'registration' => 'Inscription',
            'uniform' => 'Uniforme',
            'transport' => 'Transport',
            'meal' => 'Repas',
            'other' => 'Autre'
        ];

        return $types[$this->fee_type] ?? $this->fee_type;
    }

    /**
     * Accesseur pour la fréquence en français
     */
    public function getFrequencyLabelAttribute()
    {
        $frequencies = [
            'monthly' => 'Mensuel',
            'quarterly' => 'Trimestriel',
            'yearly' => 'Annuel',
            'one_time' => 'Unique'
        ];

        return $frequencies[$this->frequency] ?? $this->frequency;
    }

    /**
     * Accesseur pour le statut en français
     */
    public function getStatusLabelAttribute()
    {
        return $this->is_active ? 'Actif' : 'Inactif';
    }

    /**
     * Accesseur pour la couleur du statut
     */
    public function getStatusColorAttribute()
    {
        return $this->is_active ? 'success' : 'secondary';
    }

    /**
     * Accesseur pour la couleur du type de frais
     */
    public function getTypeColorAttribute()
    {
        $colors = [
            'tuition' => 'primary',
            'registration' => 'warning',
            'uniform' => 'info',
            'transport' => 'success',
            'meal' => 'danger',
            'other' => 'secondary'
        ];

        return $colors[$this->fee_type] ?? 'secondary';
    }

    /**
     * Accesseur pour la couleur de la fréquence
     */
    public function getFrequencyColorAttribute()
    {
        $colors = [
            'monthly' => 'primary',
            'quarterly' => 'info',
            'yearly' => 'success',
            'one_time' => 'warning'
        ];

        return $colors[$this->frequency] ?? 'secondary';
    }

    /**
     * Accesseur pour le type de frais en français (alias)
     */
    public function getTypeLabelAttribute()
    {
        return $this->fee_type_label;
    }
}
