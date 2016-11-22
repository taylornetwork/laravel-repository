<?php

namespace TaylorNetwork\LaravelRepository\Commands;

use Illuminate\Console\GeneratorCommand;

class ClassMakeCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'make:repositoryClass {name} {--driver=default}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new repository class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Repository Class';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/stubs/class.stub';
    }
    /**
     * Get the default namespace for the class.
     *
     * @param  string $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\\'. $this->getConfigNamespace();
    }

    /**
     * Get the namespace from config
     * 
     * @return mixed
     */
    protected function getConfigNamespace()
    {
        $directory = config('repository.directory', 'Repositories');
        $namespace = str_replace('{directory}', $directory, config('repository.namespace', '{directory}\\{name}'));
        return str_replace('{name}', $this->getNameInput(), $namespace);
    }

    /**
     * Get the name from the config naming convention
     * 
     * @return mixed
     */
    protected function getConfigName()
    {
        $driver = ($this->option('driver') == strtolower('default') 
                        ? config('repository.drivers.0', 'Eloquent') 
                        : ucwords($this->option('driver'))
                    );
        
        $name = str_replace('{name}', $this->getNameInput(), config('repository.naming.class', '{driver}{name}'));
        return str_replace('{driver}', $driver, $name);
    }

    /**
     * @inheritdoc
     */
    protected function getPath($name)
    {
        $name = str_replace_first($this->laravel->getNamespace(), '', $name);
        return $this->laravel['path'].'/'. str_replace('\\', '/', $name) . '.php';
    }

    /**
     * @inheritdoc
     */
    public function fire()
    {
        $name = $this->parseName($this->getConfigName());

        $path = $this->getPath($name);

        if ($this->alreadyExists($this->getConfigName())) {
            $this->error($this->type.' already exists!');

            return false;
        }

        $this->makeDirectory($path);

        $this->files->put($path, $this->buildClass($name));

        $this->info($this->type.' created successfully.');
    }

    /**
     * @inheritdoc
     */
    protected function buildClass($name)
    {
        $stub = $this->files->get($this->getStub());
        return $this->replaceNamespace($stub, $name)->replaceContract($stub)->replaceClass($stub, $name);
    }

    /**
     * Replace the contract name in stub
     * 
     * @param $stub
     * @return $this
     */
    protected function replaceContract(&$stub)
    {
        $stub = str_replace(
                    'DummyContract',
                    str_replace(
                        '{name}',
                        $this->getNameInput(),
                        config('repository.naming.contract', '{name}Repository')
                    ),
                    $stub
                );
        return $this;
    }
}
