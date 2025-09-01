<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentGateway extends Model
{
    protected $fillable = [
        'name',
        'code',
        'description',
        'config',
        'logo_url',
        'color',
        'is_active',
        'is_test_mode',
        'transaction_fee',
        'fixed_fee',
        'min_amount',
        'max_amount',
        'metadata'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_test_mode' => 'boolean',
        'transaction_fee' => 'decimal:2',
        'fixed_fee' => 'decimal:2',
        'min_amount' => 'decimal:2',
        'max_amount' => 'decimal:2',
        'config' => 'array',
        'metadata' => 'array'
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCode($query, $code)
    {
        return $query->where('code', $code);
    }

    // Accesseurs
    public function getStatusLabelAttribute(): string
    {
        return $this->is_active ? 'Actif' : 'Inactif';
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return $this->is_active ? 'bg-success' : 'bg-danger';
    }

    public function getModeLabelAttribute(): string
    {
        return $this->is_test_mode ? 'Test' : 'Production';
    }

    public function getModeBadgeClassAttribute(): string
    {
        return $this->is_test_mode ? 'bg-warning' : 'bg-success';
    }

    // Méthodes utilitaires
    public function calculateFees($amount): float
    {
        $percentageFee = ($amount * $this->transaction_fee) / 100;
        return $percentageFee + $this->fixed_fee;
    }

    public function getTotalAmount($baseAmount): float
    {
        return $baseAmount + $this->calculateFees($baseAmount);
    }

    public function isValidAmount($amount): bool
    {
        return $amount >= $this->min_amount && $amount <= $this->max_amount;
    }

    public function getConfigValue($key, $default = null)
    {
        return $this->config[$key] ?? $default;
    }

    public function setConfigValue($key, $value): bool
    {
        $config = $this->config ?? [];
        $config[$key] = $value;
        return $this->update(['config' => $config]);
    }

    // Méthodes statiques pour les passerelles populaires
    public static function getMoovMoney(): ?self
    {
        return self::byCode('moov_money')->active()->first();
    }

    public static function getAirtelMoney(): ?self
    {
        return self::byCode('airtel_money')->active()->first();
    }

    public static function getActiveGateways()
    {
        return self::active()->orderBy('name')->get();
    }

    // Méthode pour obtenir les passerelles disponibles pour un montant
    public static function getAvailableForAmount($amount)
    {
        return self::active()
            ->where('min_amount', '<=', $amount)
            ->where('max_amount', '>=', $amount)
            ->orderBy('name')
            ->get();
    }
}
