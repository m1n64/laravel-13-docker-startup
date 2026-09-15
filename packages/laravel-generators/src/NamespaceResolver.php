<?php

declare(strict_types=1);

namespace Package\LaravelGenerators;

use Illuminate\Contracts\Config\Repository;

/**
 * Resolves target namespaces for Clean Architecture generators.
 */
final class NamespaceResolver
{
    /**
     * @param Repository $config The configuration repository.
     */
    public function __construct(
        private readonly Repository $config,
    ) {
    }

    /**
     * Returns the configured namespace for the given generator type.
     *
     * @param string $type The generator type.
     * @param string $rootNamespace The application root namespace.
     * @return string The full namespace.
     */
    public function resolve(string $type, string $rootNamespace): string
    {
        $namespace = $this->config->get("generators.namespaces.{$type}");

        return $rootNamespace.'\\'.$namespace;
    }
}
