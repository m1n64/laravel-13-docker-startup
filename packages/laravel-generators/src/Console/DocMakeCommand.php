<?php

declare(strict_types=1);

namespace Package\LaravelGenerators\Console;

use Illuminate\Console\GeneratorCommand;
use Package\LaravelGenerators\NamespaceResolver;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'make:doc')]
final class DocMakeCommand extends GeneratorCommand
{
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new OpenAPI doc class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Doc';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub(): string
    {
        return __DIR__.'/../../stubs/doc.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param string $rootNamespace The root application namespace.
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace): string
    {
        return $this->laravel->make(NamespaceResolver::class)->resolve('doc', $rootNamespace);
    }
}
