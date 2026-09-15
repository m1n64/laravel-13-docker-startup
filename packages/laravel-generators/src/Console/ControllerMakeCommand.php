<?php

declare(strict_types=1);

namespace Package\LaravelGenerators\Console;

use Illuminate\Routing\Console\ControllerMakeCommand as BaseControllerMakeCommand;
use Package\LaravelGenerators\NamespaceResolver;

/**
 * Generates controllers in the Transport layer.
 */
final class ControllerMakeCommand extends BaseControllerMakeCommand
{
    /**
     * Get the default namespace for the class.
     *
     * @param string $rootNamespace The root application namespace.
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace): string
    {
        return $this->laravel->make(NamespaceResolver::class)->resolve('controller', $rootNamespace);
    }
}
