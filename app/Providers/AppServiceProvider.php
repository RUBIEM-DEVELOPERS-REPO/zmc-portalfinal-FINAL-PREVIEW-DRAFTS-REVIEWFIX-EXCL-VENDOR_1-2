<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use App\Models\Application as ApplicationModel;
use App\Observers\ApplicationObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Console commands (Laravel 11 doesn't use app/Console/Kernel by default)
        if ($this->app->runningInConsole()) {
            $this->commands([
                \App\Console\Commands\ProcessAccreditationExpiries::class,
            ]);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Set default pagination view to Bootstrap 5 (without SVG arrows)
        \Illuminate\Pagination\Paginator::defaultView('vendor.pagination.bootstrap-5');
        \Illuminate\Pagination\Paginator::defaultSimpleView('vendor.pagination.bootstrap-5');
        
        // App locale (session overrides user)
        try {
            $sessionLocale = request()->session()->get('app_locale');
            $userLocale = Auth::check() ? (Auth::user()->locale ?? null) : null;
            $locale = $sessionLocale ?: $userLocale ?: 'en';
            App::setLocale($locale);
        } catch (\Throwable $e) {
            // ignore
        }

        // Application status/payment notifications
        ApplicationModel::observe(ApplicationObserver::class);

        /*
        |--------------------------------------------------------------------------
        | Admin counts in STAFF sidebar (Super Admin uses staff theme)
        |--------------------------------------------------------------------------
        | We do NOT show a separate admin sidebar. Instead, we inject admin menu
        | items + live counters into the existing staff sidebar when the logged-in
        | user has the super_admin role.
        */
        View::composer('layouts.sidebar_staff', function ($view) {
            try {
                $user = Auth::user();

                if (!$user || !$user->hasAnyRole(['super_admin', 'it_admin', 'director'])) {
                    return;
                }

                // Application counts
                $mediaHouseTotal = ApplicationModel::where('application_type', 'registration')->count();
                $accreditationTotal = ApplicationModel::where('application_type', 'accreditation')->count();

                // "Pending" means anything submitted/in-review but not finalized
                $pendingStatuses = [
                    ApplicationModel::SUBMITTED,
                    ApplicationModel::OFFICER_REVIEW,
                    ApplicationModel::REGISTRAR_REVIEW,
                    ApplicationModel::ACCOUNTS_REVIEW,
                    ApplicationModel::PRODUCTION_QUEUE,
                ];

                $pendingTotal = ApplicationModel::whereIn('status', $pendingStatuses)->count();
                $pendingMediaHouse = ApplicationModel::where('application_type', 'registration')
                    ->whereIn('status', $pendingStatuses)
                    ->count();
                $pendingAccreditation = ApplicationModel::where('application_type', 'accreditation')
                    ->whereIn('status', $pendingStatuses)
                    ->count();

                $view->with('admin_sidebar_counts', [
                    'mediahouse_total' => $mediaHouseTotal,
                    'accreditation_total' => $accreditationTotal,
                    'pending_total' => $pendingTotal,
                    'pending_mediahouse' => $pendingMediaHouse,
                    'pending_accreditation' => $pendingAccreditation,
                ]);
            } catch (\Throwable $e) {
                // ignore (sidebar should still render)
            }
        });
    }
}
