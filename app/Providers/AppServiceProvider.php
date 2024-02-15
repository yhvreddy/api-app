<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

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
        Validator::extend('alpha_spaces', function ($attribute, $value) {
            return preg_match('/^[a-zA-Z\s]+$/', $value);
        });

        Validator::extend('uppercase', function ($attribute, $value) {
            return strtoupper($value) === $value;
        });
    }
}
