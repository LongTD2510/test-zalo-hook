<?php

namespace App\Providers;

use Dotenv\Repository\RepositoryInterface;
use Illuminate\Support\ServiceProvider;
use App\Repositories;
use App\RepositoryInterfaces;

class RepositoryServiceProvider extends ServiceProvider
{
    protected $repositorySingleton = [];

    protected $repositoryBindings = [
        RepositoryInterfaces\CategoryRepositoryInterface::class => Repositories\CategoryRepository::class,
        RepositoryInterfaces\PostRepositoryInterface::class => Repositories\PostRepository::class,
        RepositoryInterfaces\ConfigRepositoryInterface::class => Repositories\ConfigRepository::class,
        RepositoryInterfaces\TemplateRepositoryInterface::class => Repositories\TemplateRepository::class,
    ];

    public function register(): void
    {
        foreach ($this->repositoryBindings as $interface => $implementation) {
            $this->app->bind($interface, $implementation);
        }

        foreach ($this->repositorySingleton as $interface => $implementation) {
            $this->app->singleton($interface, function () use ($implementation) {
                return $implementation::getInstance();
            });
        }
    }
}
