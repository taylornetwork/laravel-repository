<?php

namespace TaylorNetwork\LaravelRepository\Commands;

use Illuminate\Console\GeneratorCommand;

class ContractMakeCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:repositoryContract';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new repository contract';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Repository Contract';
    
    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/stubs/contract.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\\' . $this->getConfigNamespace();
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
        return str_replace('{name}', $this->getNameInput(), config('repository.naming.contract', '{name}Repository'));
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
        $name = $this->qualifyClass($this->getConfigName());

        $path = $this->getPath($name);

        if ($this->alreadyExists($this->getConfigName())) {
            $this->error($this->type.' already exists!');

            return false;
        }

        $this->makeDirectory($path);

        $this->files->put($path, $this->buildClass($name));

        $this->info($this->type.' created successfully.');
    }

}
