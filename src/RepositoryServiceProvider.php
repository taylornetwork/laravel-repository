<?php

namespace TaylorNetwork\Repository;

use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    private $directory;

    private $dirPath;

    private $namespace;

    private $naming;

    private $drivers;

    public function __construct(Application $app)
    {
        parent::__construct($app);

        $this->directory = config('repository.directory', 'Repositories');
        
        $this->dirPath = app_path() . '/' . $this->directory;
        
        $this->namespace = 'App\\'.str_replace('{directory}',$this->directory,config('repository.namespace','{directory}')).'\\';
        
        $this->naming = config('repository.naming', [ 'interface' => '{name}Repository', 'class' => '{driver}{name}' ]);
        
        $this->drivers = config('repository.drivers', [ 'Eloquent' ]);
    }

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/config/repository.php' => config_path('repository.php'),
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/config/repository.php', 'repository'
        );

        //$this->commands([ ]); @todo Actually make the commands work

        $this->registerRepositories();

    }

    /**
     * Replace namespace
     *
     * @param string $name
     * @return string
     */
    public function replaceNamespace($name)
    {
        return str_replace('{name}', $name, $this->namespace);
    }

    /**
     * Get the interface name
     *
     * @param string $name
     * @return string
     */
    public function getInterface($name)
    {
        return $this->replaceNamespace($name) . str_replace('{name}', $name, $this->naming['interface']);
    }

    /**
     * Get the class name
     *
     * @param string $name
     * @param string $path
     * @return string
     */
    public function getClass($name, $path)
    {
        $class = str_replace('{name}', $name, $this->naming['class']);
        $files = [];

        foreach(glob($path . '/*.php') as $file)
        {
            $files[] = substr(last(explode('/', $file)), 0, -4);
        }

        foreach($this->drivers as $driver)
        {
            $replaceDriver = str_replace('{driver}', $driver, $class);
            if(in_array($replaceDriver, $files))
            {
                $class = $replaceDriver;
                break;
            }
        }

        return $this->replaceNamespace($name).$class;
    }

    /**
     * Register the repositories
     */
    public function registerRepositories()
    {
        if(preg_match('/{name}/', $this->namespace))
        {
            foreach(glob($this->dirPath . '/*') as $repositoryName)
            {
                $path = $this->dirPath . '/' . $repositoryName;
                $this->app->singleton($this->getInterface($repositoryName), $this->getClass($repositoryName, $path));
            }
        }
        else
        {
            // @todo figure this out
        }
    }
}
