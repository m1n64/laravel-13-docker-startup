<?php

declare(strict_types=1);

namespace App\Infrastructure\Provider;

use Illuminate\Support\ServiceProvider;

/**
 * Dedicated service provider for binding interfaces to implementations.
 */
class BindingServiceProvider extends ServiceProvider
{
    /**
     * Register any application service bindings.
     */
    public function register(): void
    {
        //
    }
}
