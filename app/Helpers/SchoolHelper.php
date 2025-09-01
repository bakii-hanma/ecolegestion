<?php

namespace App\Helpers;

use App\Models\SchoolSettings;

class SchoolHelper
{
    /**
     * Obtenir les paramètres de l'école
     */
    public static function getSettings()
    {
        return SchoolSettings::getSettings();
    }

    /**
     * Obtenir le nom de l'école
     */
    public static function getName()
    {
        return self::getSettings()->school_name;
    }

    /**
     * Obtenir le logo de l'école
     */
    public static function getLogo()
    {
        return self::getSettings()->logo_url;
    }

    /**
     * Obtenir le sceau de l'école
     */
    public static function getSeal()
    {
        return self::getSettings()->seal_url;
    }

    /**
     * Obtenir l'année scolaire
     */
    public static function getAcademicYear()
    {
        return self::getSettings()->academic_year;
    }

    /**
     * Obtenir le titre du directeur
     */
    public static function getPrincipalTitle()
    {
        return self::getSettings()->principal_title;
    }

    /**
     * Obtenir le nom du directeur
     */
    public static function getPrincipalName()
    {
        return self::getSettings()->principal_name;
    }

    /**
     * Obtenir les informations de contact
     */
    public static function getContactInfo()
    {
        $settings = self::getSettings();
        return [
            'phone' => $settings->school_phone,
            'email' => $settings->school_email,
            'website' => $settings->school_website,
            'bp' => $settings->school_bp,
            'address' => $settings->school_address,
        ];
    }
}
