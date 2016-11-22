<?php

return [
    /*
     |--------------------------------------------------------------------------
     | Base Directory
     |--------------------------------------------------------------------------
     |
     | This is where your repositories will be stored.
     | 
     | 'App\' will be prepended, '\' will be appended.
     | 
     */
    'directory' => 'Repositories',

    /*
     |--------------------------------------------------------------------------
     | Repository Namespace Pattern
     |--------------------------------------------------------------------------
     |
     | Use this to make additional folders in which to keep each repository.
     | {directory} will be replaced by value above 
     | {name} will be replaced by a repository name 
     |
     | Examples: 
     |      User repository in App\Repositories\User\UserRepository
     |      Use '{directory}\\{name}' 
     | 
     |      User repository in App\Repositories\UserRepository
     |      Use '{directory}'
     |
     |      User repository in App\Repositories\RepositoryForUser\UserRepository
     |      Use '{directory}\\RepositoryFor{name}'
     |      
     */
    'namespace' => '{directory}\\{name}',

    /*
     |--------------------------------------------------------------------------
     | Naming Convention
     |--------------------------------------------------------------------------
     |
     | How you intend on naming your repositories.
     | {name} is replaced by repository name.
     | {driver} is replaced by a driver in the driver array below.
     |
     */
    'naming' => [
        'contract' => '{name}Repository',
        'class' => '{driver}{name}',
    ],

    /*
     |--------------------------------------------------------------------------
     | Drivers
     |--------------------------------------------------------------------------
     |
     | The drivers that you will use in order of search. Service provider will 
     | look for the first value and if one is found using the naming convention
     | above it will add that as a singleton. 
     |
     | Note: When you run 'php artisan make:repository Model' with no driver 
     |      option, the first value in this array is used.
     |
     */
    'drivers' => [
        'Eloquent',
        'Storage',
    ],
];