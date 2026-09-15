<?php

declare(strict_types=1);

namespace Package\LaravelGenerators;

use Illuminate\Support\ServiceProvider;
use Package\LaravelGenerators\Console;

/**
 * Registers Clean Architecture-aware code generators.
 */
final class GeneratorServiceProvider extends ServiceProvider
{
    /**
     * Register the package configuration.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/generators.php', 'generators');
    }

    /**
     * Boot the package and register all generator commands.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Console\ControllerMakeCommand::class,
                Console\ModelMakeCommand::class,
                Console\RequestMakeCommand::class,
                Console\ResourceMakeCommand::class,
                Console\ProviderMakeCommand::class,
                Console\ExceptionMakeCommand::class,
                Console\EnumMakeCommand::class,
                Console\CommandMakeCommand::class,
                Console\ServiceMakeCommand::class,
                Console\ActionMakeCommand::class,
                Console\DocMakeCommand::class,
            ]);
        }
    }
}
