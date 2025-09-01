<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\SchoolSettings;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Partager les paramètres de l'établissement avec toutes les vues
        View::composer('*', function ($view) {
            $schoolSettings = SchoolSettings::getSettings();
            $view->with('schoolSettings', $schoolSettings);
        });
    }
}
