<?php

declare(strict_types=1);

namespace Package\LaravelGenerators\Console;

use Illuminate\Foundation\Console\ModelMakeCommand as BaseModelMakeCommand;
use Package\LaravelGenerators\NamespaceResolver;

/**
 * Generates models in the Domain layer.
 */
final class ModelMakeCommand extends BaseModelMakeCommand
{
    /**
     * Get the default namespace for the class.
     *
     * @param string $rootNamespace The root application namespace.
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace): string
    {
        return $this->laravel->make(NamespaceResolver::class)->resolve('model', $rootNamespace);
    }
}
