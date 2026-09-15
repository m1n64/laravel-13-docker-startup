<?php

declare(strict_types=1);

namespace Package\LaravelGenerators\Console;

use Illuminate\Foundation\Console\EnumMakeCommand as BaseEnumMakeCommand;
use Package\LaravelGenerators\NamespaceResolver;

/**
 * Generates enums in the Domain layer.
 */
final class EnumMakeCommand extends BaseEnumMakeCommand
{
    /**
     * Get the default namespace for the class.
     *
     * @param string $rootNamespace The root application namespace.
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace): string
    {
        return $this->laravel->make(NamespaceResolver::class)->resolve('enum', $rootNamespace);
    }
}
