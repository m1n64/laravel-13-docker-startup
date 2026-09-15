<?php

declare(strict_types=1);

namespace Package\LaravelGenerators\Console;

use Illuminate\Foundation\Console\ResourceMakeCommand as BaseResourceMakeCommand;
use Package\LaravelGenerators\NamespaceResolver;

/**
 * Generates API resources in the Transport layer.
 */
final class ResourceMakeCommand extends BaseResourceMakeCommand
{
    /**
     * Get the default namespace for the class.
     *
     * @param string $rootNamespace The root application namespace.
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace): string
    {
        return $this->laravel->make(NamespaceResolver::class)->resolve('resource', $rootNamespace);
    }
}
