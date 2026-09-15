<?php

declare(strict_types=1);

namespace Package\LaravelGenerators\Console;

use Illuminate\Foundation\Console\ConsoleMakeCommand as BaseConsoleMakeCommand;
use Package\LaravelGenerators\NamespaceResolver;

/**
 * Generates console commands in the Infrastructure layer.
 */
final class CommandMakeCommand extends BaseConsoleMakeCommand
{
    /**
     * Get the default namespace for the class.
     *
     * @param string $rootNamespace The root application namespace.
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace): string
    {
        return $this->laravel->make(NamespaceResolver::class)->resolve('command', $rootNamespace);
    }
}
