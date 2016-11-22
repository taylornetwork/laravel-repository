<?php

namespace TaylorNetwork\LaravelRepository;

use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use TaylorNetwork\LaravelRepository\Commands\ClassMakeCommand;
use TaylorNetwork\LaravelRepository\Commands\ContractMakeCommand;
use TaylorNetwork\LaravelRepository\Commands\RepositoryMakeCommand;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Repository Directory
     *
     * @var string
     */
    private $directory;

    /**
     * Path to Directory
     *
     * @var string
     */
    private $dirPath;

    /**
     * Namespace
     *
     * @var string
     */
    private $namespace;

    /**
     * Naming Convention
     *
     * @var array
     */
    private $naming;

    /**
     * Drivers
     *
     * @var array
     */
    private $drivers;

    /**
     * RepositoryServiceProvider constructor.
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        parent::__construct($app);

        $this->mergeConfigFrom(
            __DIR__.'/config/repository.php', 'repository'
        );

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
        $this->commands([ 
            RepositoryMakeCommand::class,
            ClassMakeCommand::class,
            ContractMakeCommand::class
        ]);

        $this->registerRepositories();
    }

    /**
     * Fix name
     *
     * @param string $name
     * @return string
     */
    public function fixName($name)
    {
        return last(explode('/', $name));
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
                $name = $this->fixName($repositoryName);
                $path = $this->dirPath . '/' . $name;
                $this->app->singleton($this->getInterface($name), $this->getClass($name, $path));
            }
        }
        else
        {
            $baseInterface = str_replace('{name}', '', $this->naming['interface']);
            foreach(glob($this->dirPath . '/*.php') as $fileName)
            {
                $file = substr($this->fixName($fileName), 0, -4);
                if(preg_match('/' . $baseInterface . '/', $file))
                {
                    $name = str_replace($baseInterface, '', $file);
                    $this->app->singleton($this->getInterface($name), $this->getClass($name, $this->dirPath));
                }
            }

        }
    }
}
