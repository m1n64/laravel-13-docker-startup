<?php

declare(strict_types=1);

namespace Package\LaravelGenerators\Console;

use Illuminate\Foundation\Console\ProviderMakeCommand as BaseProviderMakeCommand;
use Package\LaravelGenerators\NamespaceResolver;

/**
 * Generates service providers in the Infrastructure layer.
 */
final class ProviderMakeCommand extends BaseProviderMakeCommand
{
    /**
     * Get the default namespace for the class.
     *
     * @param string $rootNamespace The root application namespace.
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace): string
    {
        return $this->laravel->make(NamespaceResolver::class)->resolve('provider', $rootNamespace);
    }
}
