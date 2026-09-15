<?php

declare(strict_types=1);

namespace App\Infrastructure\Provider;

use Illuminate\Cache\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\ServiceProvider;

/**
 * Configures rate limiters for the application.
 */
final class RateLimitProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap rate limiters.
     *
     * @param RateLimiter $rateLimiter The rate limiter service.
     */
    public function boot(RateLimiter $rateLimiter): void
    {
        $rateLimiter->for('api', fn (): Limit => Limit::perMinute(60));
    }
}
