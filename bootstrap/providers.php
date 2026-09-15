<?php

return [
    App\Infrastructure\Provider\AppServiceProvider::class,
    App\Infrastructure\Provider\BindingServiceProvider::class,
    App\Infrastructure\Provider\RateLimitProvider::class,
    App\Infrastructure\Provider\HorizonServiceProvider::class,
    Package\LaravelGenerators\GeneratorServiceProvider::class,
];
