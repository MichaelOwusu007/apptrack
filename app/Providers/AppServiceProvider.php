<?php

namespace App\Providers;

use App\Models\Activity;
use App\Policies\ActivityPolicy;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    protected $policies = [
        Activity::class => ActivityPolicy::class,
    ];

    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // Use our custom Blade pagination view
        Paginator::defaultView('components.pagination');

        // Register policies
        foreach ($this->policies as $model => $policy) {
            Gate::policy($model, $policy);
        }

        // Admin gate shorthand
        Gate::define('admin', fn($user) => $user->hasRole('admin'));
        Gate::define('supervisor', fn($user) => $user->hasAnyRole(['admin', 'supervisor']));
    }
}
