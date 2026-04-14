<?php

namespace App\Providers;

use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Models\ActivityLog;
use App\Models\Patient;
use App\Models\RendezVous;
use App\Models\DocumentPatient;
use App\Models\Consultation;
use App\Policies\PatientPolicy;
use App\Policies\RendezVousPolicy;
use App\Policies\DocumentPatientPolicy;
use App\Policies\ConsultationPolicy;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Rate Limiters
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('patient-rdv', function (Request $request) {
            return Limit::perMinute(10)->by($request->user()?->id);
        });

        View::composer('layouts.*', function ($view) {
            $clinic = auth()->user()?->clinic;
            $view->with('clinicPrimaryColor', $clinic?->getPrimaryColorOrDefault() ?? '#1e3a8a');
            $view->with('clinicSecondaryColor', $clinic?->getSecondaryColorOrDefault() ?? '#f97316');
            $view->with('clinicSidebarTextColor', $clinic?->getSidebarTextColorOrDefault() ?? '#ffffff');
            $view->with('clinicLogoUrl', $clinic?->logo_url);
            $view->with('clinicName', $clinic?->name ?? 'MonRDV');
        });

        // Policy registration
        Gate::policy(Patient::class, PatientPolicy::class);
        Gate::policy(RendezVous::class, RendezVousPolicy::class);
        Gate::policy(DocumentPatient::class, DocumentPatientPolicy::class);
        Gate::policy(Consultation::class, ConsultationPolicy::class);

        // Audit des echecs d'authentification (RGPD / securite).
        // ActivityLog::log() court-circuite quand auth()->check() est faux,
        // donc on ecrit directement la ligne.
        Event::listen(function (Failed $event) {
            ActivityLog::create([
                'user_id'     => $event->user?->id,
                'action'      => 'connexion_echec',
                'model_type'  => 'User',
                'model_id'    => $event->user?->id,
                'description' => "Echec de connexion pour {$event->credentials['email']}",
                'ip_address'  => request()->ip(),
            ]);
        });

        Event::listen(function (Lockout $event) {
            ActivityLog::create([
                'action'      => 'connexion_bloquee',
                'description' => "Tentatives trop nombreuses pour {$event->request->input('email')}",
                'ip_address'  => request()->ip(),
            ]);
        });
    }
}
