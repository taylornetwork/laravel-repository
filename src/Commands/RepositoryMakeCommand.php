<?php

namespace TaylorNetwork\LaravelRepository\Commands;

use Illuminate\Console\Command;

class RepositoryMakeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'repository:generate {name} {--driver=default}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new repository pattern';
    
    /**
     * Create a new command instance
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $name = ucwords($this->argument('name'));
        $this->call('repository:contract', [ 'name' => $name ]);
        $this->call('repository:class', [ 'name' => $name, '--driver' => $this->option('driver') ]);
    }
    
}
