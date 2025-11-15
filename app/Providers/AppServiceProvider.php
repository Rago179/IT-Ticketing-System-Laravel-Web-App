<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator; // Ensure this import is here

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
        // Tell Laravel to use our custom file by default
        Paginator::defaultView('vendor.pagination.default');
        
        // Also use it for "simple" pagination (if you ever use simplePaginate)
        Paginator::defaultSimpleView('vendor.pagination.default');
    }
}