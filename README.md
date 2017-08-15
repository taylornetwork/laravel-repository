# Repositories for Laravel

An easy and customizable to create repositories that will be automatically registered for you.

## Install

Via Composer

``` bash
$ composer require taylornetwork/laravel-repository
```

## Setup

Add the service provider to the providers array in `config/app.php`

``` php
'providers' => [
	
	TaylorNetwork\LaravelRepository\RepositoryServiceProvider::class,
	
],
```

### Publish Config

``` bash
$ php artisan vendor:publish
```

Will add `repository.php` to your config directory.

## Usage

The service provider will automatically bind repositories to their contracts based on the settings in `config/repository.php` 

### Defaults

By default Repositories are stored in `App\Repositories\ModelName` where `ModelName` is the name of the model the repository will handle.

The default naming convention used is `ModelNameRepository` for the contract and `EloquentModelName` for the class. 

### Commands

#### make:repository

This package adds the artisan command `repository:generate` to create the repository pattern based on settings in `config/repository.php`

To create a repository for the `User` model

``` bash
$ php artisan make:repository User
```

Will create

```
- ProjectRoot
	- app
		- Repositories
			- User
				- UserRepository.php
				- EloquentUser.php

```

If you are using a driver other than `Eloquent` you can specify using the `--driver` option

``` bash
$ php artisan repository:generate User --driver=storage
```
Will create

```
- ProjectRoot
	- app
		- Repositories
			- User
				- UserRepository.php
				- StorageUser.php

```

*Note: If you plan on using other drivers add them to the `drivers` array in `config/repository.php`, the service provider will search for the class in the order of the array.*

#### make:repositoryClass

Same as `repository:generate` but will only create the class, no contract.

``` bash
$ php artisan repository:class User --driver=storage
```

Creates `app/Repositories/User/StorageUser.php`

#### make:repositoryContract

Same as `repository:generate` but will only create the contract, no class. 

``` bash
$ php artisan repository:contract User 
```

Creates `app/Repositories/User/UserRepository.php`

*Note: This command does not accept the `--driver` option.*

## Credits

- Author: [Sam Taylor][link-author]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[link-author]: https://github.com/taylornetwork
