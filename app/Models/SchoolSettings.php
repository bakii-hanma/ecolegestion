<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolSettings extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_name',
        'school_address',
        'school_phone',
        'school_email',
        'school_website',
        'school_bp',
        'school_logo',
        'school_seal',
        'school_motto',
        'school_description',
        'principal_name',
        'principal_title',
        'academic_year',
        'school_type',
        'school_level',
        'country',
        'city',
        'timezone',
        'currency',
        'language',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Obtenir les paramètres de l'école
     */
    public static function getSettings()
    {
        $settings = self::where('is_active', true)->first();
        
        if (!$settings) {
            // Créer des paramètres par défaut si aucun n'existe
            $settings = self::create([
                'school_name' => 'Lycée XXXXX',
                'school_phone' => '06037499',
                'school_bp' => 'BP: 6',
                'principal_title' => 'Le Proviseur',
                'academic_year' => '2024-2025',
                'school_type' => 'Lycée',
                'school_level' => 'Secondaire',
                'country' => 'Gabon',
                'city' => 'Libreville',
                'timezone' => 'Africa/Libreville',
                'currency' => 'FCFA',
                'language' => 'fr',
                'is_active' => true
            ]);
        }
        
        return $settings;
    }

    /**
     * Obtenir l'URL du logo
     */
    public function getLogoUrlAttribute()
    {
        if ($this->school_logo) {
            // Utiliser l'URL complète avec le bon port
            $host = request()->getHost();
            $port = request()->getPort();
            $scheme = request()->getScheme();
            
            // Si on est sur localhost, utiliser 127.0.0.1:8000
            if ($host === 'localhost' || $host === '127.0.0.1') {
                return $scheme . '://127.0.0.1:8000/storage/' . $this->school_logo;
            }
            
            return $scheme . '://' . $host . ($port != 80 && $port != 443 ? ':' . $port : '') . '/storage/' . $this->school_logo;
        }
        return null;
    }

    /**
     * Obtenir l'URL du sceau
     */
    public function getSealUrlAttribute()
    {
        if ($this->school_seal) {
            // Utiliser l'URL complète avec le bon port
            $host = request()->getHost();
            $port = request()->getPort();
            $scheme = request()->getScheme();
            
            // Si on est sur localhost, utiliser 127.0.0.1:8000
            if ($host === 'localhost' || $host === '127.0.0.1') {
                return $scheme . '://127.0.0.1:8000/storage/' . $this->school_seal;
            }
            
            return $scheme . '://' . $host . ($port != 80 && $port != 443 ? ':' . $port : '') . '/storage/' . $this->school_seal;
        }
        return null;
    }
}
