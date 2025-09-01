<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ParentAccount extends Model
{
    protected $fillable = [
        'parent_id',
        'username',
        'email',
        'password',
        'phone',
        'is_active',
        'email_verified',
        'phone_verified',
        'email_verified_at',
        'phone_verified_at',
        'verification_token',
        'reset_token',
        'reset_token_expires_at',
        'last_login_at',
        'last_login_ip',
        'notification_preferences',
        'language',
        'timezone',
        'metadata'
    ];

    protected $hidden = [
        'password',
        'verification_token',
        'reset_token'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'email_verified' => 'boolean',
        'phone_verified' => 'boolean',
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
        'reset_token_expires_at' => 'datetime',
        'last_login_at' => 'datetime',
        'notification_preferences' => 'array',
        'metadata' => 'array'
    ];

    // Relations
    public function parent(): BelongsTo
    {
        return $this->belongsTo(ParentModel::class);
    }

    // Accesseurs
    public function getFullNameAttribute(): string
    {
        return $this->parent ? $this->parent->first_name . ' ' . $this->parent->last_name : '';
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->is_active ? 'Actif' : 'Inactif';
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return $this->is_active ? 'bg-success' : 'bg-danger';
    }

    // Mutateurs
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeVerified($query)
    {
        return $query->where('email_verified', true);
    }

    // Méthodes d'authentification
    public function verifyPassword($password): bool
    {
        return Hash::check($password, $this->password);
    }

    public function generateVerificationToken(): string
    {
        $token = Str::random(64);
        $this->update(['verification_token' => $token]);
        return $token;
    }

    public function generateResetToken(): string
    {
        $token = Str::random(64);
        $this->update([
            'reset_token' => $token,
            'reset_token_expires_at' => now()->addHours(24)
        ]);
        return $token;
    }

    public function verifyEmail(): bool
    {
        return $this->update([
            'email_verified' => true,
            'email_verified_at' => now(),
            'verification_token' => null
        ]);
    }

    public function verifyPhone(): bool
    {
        return $this->update([
            'phone_verified' => true,
            'phone_verified_at' => now()
        ]);
    }

    public function updateLastLogin($ip = null): bool
    {
        return $this->update([
            'last_login_at' => now(),
            'last_login_ip' => $ip
        ]);
    }

    public function isEmailVerified(): bool
    {
        return $this->email_verified;
    }

    public function isPhoneVerified(): bool
    {
        return $this->phone_verified;
    }

    public function isResetTokenValid(): bool
    {
        return $this->reset_token && 
               $this->reset_token_expires_at && 
               $this->reset_token_expires_at->isFuture();
    }

    // Méthodes pour les notifications
    public function getNotificationPreference($type, $default = true): bool
    {
        $preferences = $this->notification_preferences ?? [];
        return $preferences[$type] ?? $default;
    }

    public function setNotificationPreference($type, $enabled): bool
    {
        $preferences = $this->notification_preferences ?? [];
        $preferences[$type] = $enabled;
        
        return $this->update(['notification_preferences' => $preferences]);
    }

    // Méthode pour obtenir les enfants du parent
    public function getChildren()
    {
        return $this->parent ? $this->parent->students : collect();
    }

    // Méthode pour obtenir les paiements du parent
    public function getPayments()
    {
        return \App\Models\OnlinePayment::where('parent_id', $this->parent_id)->get();
    }
}
