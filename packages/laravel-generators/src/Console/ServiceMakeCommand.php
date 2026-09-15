<?php

declare(strict_types=1);

namespace Package\LaravelGenerators\Console;

use Illuminate\Console\GeneratorCommand;
use Package\LaravelGenerators\NamespaceResolver;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'make:service')]
final class ServiceMakeCommand extends GeneratorCommand
{
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new application service class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Service';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub(): string
    {
        return __DIR__.'/../../stubs/service.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param string $rootNamespace The root application namespace.
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace): string
    {
        return $this->laravel->make(NamespaceResolver::class)->resolve('service', $rootNamespace);
    }
}
