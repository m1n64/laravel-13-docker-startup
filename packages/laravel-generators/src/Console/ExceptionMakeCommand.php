<?php

declare(strict_types=1);

namespace Package\LaravelGenerators\Console;

use Illuminate\Foundation\Console\ExceptionMakeCommand as BaseExceptionMakeCommand;
use Package\LaravelGenerators\NamespaceResolver;

/**
 * Generates exceptions in the Domain layer.
 */
final class ExceptionMakeCommand extends BaseExceptionMakeCommand
{
    /**
     * Get the default namespace for the class.
     *
     * @param string $rootNamespace The root application namespace.
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace): string
    {
        return $this->laravel->make(NamespaceResolver::class)->resolve('exception', $rootNamespace);
    }
}
