<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $dir = app_path() . '/Repositories';

        foreach(scandir($dir) as $repository)
        {
            if(substr($repository, 0, 1) != '.')
            {
                $this->app->singleton(
                    "App\\Repositories\\{$repository}\\{$repository}Repository",
                    "App\\Repositories\\{$repository}\\Eloquent{$repository}"
                );
            }
        }
    }
}
