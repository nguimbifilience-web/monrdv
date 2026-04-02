<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        View::composer('layouts.*', function ($view) {
            $clinic = auth()->user()?->clinic;
            $view->with('clinicPrimaryColor', $clinic?->getPrimaryColorOrDefault() ?? '#1e3a8a');
            $view->with('clinicSecondaryColor', $clinic?->getSecondaryColorOrDefault() ?? '#f97316');
            $view->with('clinicLogoUrl', $clinic?->logo_url);
            $view->with('clinicName', $clinic?->name ?? 'MonRDV');
        });
    }
}
