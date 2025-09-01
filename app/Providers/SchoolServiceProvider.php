<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Helpers\SchoolHelper;

class SchoolServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Partager les paramètres de l'école avec toutes les vues
        view()->composer('*', function ($view) {
            $view->with('schoolSettings', SchoolHelper::getSettings());
        });
    }
}
