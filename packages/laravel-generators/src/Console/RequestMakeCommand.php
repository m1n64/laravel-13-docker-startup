<?php

declare(strict_types=1);

namespace Package\LaravelGenerators\Console;

use Illuminate\Foundation\Console\RequestMakeCommand as BaseRequestMakeCommand;
use Package\LaravelGenerators\NamespaceResolver;

/**
 * Generates form requests in the Transport layer.
 */
final class RequestMakeCommand extends BaseRequestMakeCommand
{
    /**
     * Get the default namespace for the class.
     *
     * @param string $rootNamespace The root application namespace.
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace): string
    {
        return $this->laravel->make(NamespaceResolver::class)->resolve('request', $rootNamespace);
    }
}
