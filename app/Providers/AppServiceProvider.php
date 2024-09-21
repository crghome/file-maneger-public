<?php

namespace App\Providers;

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

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
        // Auth::extend('jwt', function (Application $app, string $name, array $config){
        //     // Return an instance of Illuminate\Contracts\Auth\Guard...
        //     return new JwtGuard(Auth::createUserProvider($config['provider']));
        // });
    }
}
