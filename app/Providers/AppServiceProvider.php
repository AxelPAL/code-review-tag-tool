<?php

namespace App\Providers;

use Cache;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (Cache::has('BITBUCKET_TOKEN')) {
            config(['bitbucket.connections.main.token' => Cache::get('BITBUCKET_TOKEN')]);
        }
    }
}
