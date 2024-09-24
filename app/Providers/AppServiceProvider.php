<?php

namespace App\Providers;

use App\Services\CacheInterface;
use App\Services\CacheSession;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(CacheInterface::class, function ($app) {
            return new CacheSession();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
