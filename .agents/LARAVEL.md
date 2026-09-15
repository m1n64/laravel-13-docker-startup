# Laravel 13 Specifics

**Framework:** Laravel 13.x
**PHP Requirement:** PHP 8.5 (STRICTLY)

Reference: https://laravel.com/docs/13.x

## STRICTLY FORBIDDEN

### No Facades

The use of Laravel Facades is STRICTLY FORBIDDEN in any layer (Services, Actions, Repositories, Controllers).

- FORBIDDEN: `DB::`, `Cache::`, `Config::`, `Request::`, `Route::`, `Log::`, `Event::`, `Queue::`, `Redis::`, `Session::`, `Auth::`, `Validator::`, `Hash::`, `Crypt::`, `Storage::`, `URL::`, `View::`, `Artisan::`
- Instead: Inject the corresponding PSR-compliant interface or utility class via the constructor.

**Mapping of Facades to injectable alternatives:**

| Facade | Injectable Alternative |
|---|---|
| `DB` | `Illuminate\Database\ConnectionInterface` or `Illuminate\Database\DatabaseManager` |
| `Cache` | `Illuminate\Contracts\Cache\Repository` |
| `Config` | `Illuminate\Contracts\Config\Repository` |
| `Log` | `Psr\Log\LoggerInterface` |
| `Event` | `Illuminate\Events\Dispatcher` |
| `Queue` | `Illuminate\Contracts\Queue\Queue` |
| `Redis` | `Illuminate\Redis\Connections\Connection` |
| `Session` | `Illuminate\Session\SessionManager` |
| `Auth` | `Illuminate\Contracts\Auth\Guard` or `Illuminate\Contracts\Auth\Factory` |
| `Validator` | `Illuminate\Contracts\Validation\Factory` |
| `Hash` | `Illuminate\Contracts\Hashing\Hasher` |
| `Crypt` | `Illuminate\Contracts\Encryption\Encrypter` |
| `Storage` | `Illuminate\Contracts\Filesystem\Filesystem` |
| `URL` | `Illuminate\Routing\UrlGenerator` |
| `View` | `Illuminate\Contracts\View\Factory` |
| `Artisan` | `Illuminate\Console\Application` or `Illuminate\Contracts\Console\Kernel` |

### No Global Helpers

NEVER use `app()`, `resolve()`, `auth()`, `request()`, `session()`, `config()`, `env()`, `view()`, `route()`, `asset()`, `url()`, `redirect()`, `response()`, `back()`, `old()`, `csrf_token()`, `csrf_field()`, `method_field()`, `now()`, `today()` or any other global helper functions inside Controllers, Services, Repositories, or Actions.

- `env()` is ONLY allowed in configuration files under `config/`. NEVER in application code.
- `config()` is ONLY allowed in configuration files. In application code, inject `Illuminate\Contracts\Config\Repository`.

### No Magic

- **No implicit route model binding with magic resolution.** Use explicit binding via route configuration or controller method signatures with typed parameters.
- **No `$request->all()` or mass assignment.** Always use `FormRequest` with explicitly defined rules and `$request->validated()`.
- **No `response()->json([...])` or helper `response()`.** All API responses MUST be handled via `JsonResource` or `JsonResource::collection`.
- **No `$this->middleware()` in controllers.** All middleware MUST be applied in `routes/` files.
- **No `with()` for eager loading inside controllers.** Eager loading belongs in the Repository layer.

### No Eloquent in Business Logic

- Eloquent models are FORBIDDEN in Services and Actions.
- All data manipulation MUST happen within Repository methods.
- Services receive Dto objects, not Eloquent models.
- Repositories MAY return Eloquent models, Dto objects, or simple typed values — choose the simplest approach that keeps the Service layer clean. Mapping to Dto is encouraged when crossing domain boundaries, but is not mandatory for every call.

## ENCOURAGED

### Dependency Injection

- Use **strict constructor injection** for all dependencies.
- Bind Repository interfaces to implementations in `App\Infrastructure\Provider\BindingServiceProvider` (a dedicated provider for all interface-to-implementation bindings).
- Use `readonly` properties for injected dependencies.

### Attributes Over Magic

- Use PHP 8 attributes for metadata instead of PHPDoc annotations where Laravel supports them.
- **Route attributes are MANDATORY.** This project uses `spatie/laravel-route-attributes` (https://github.com/spatie/laravel-route-attributes) for attribute-based routing. All routes MUST be defined using attributes directly on controller methods. NEVER use `routes/web.php` or `routes/api.php` for new routes.
- Available attributes:
    - **HTTP verbs:** `#[Get('uri')]`, `#[Post('uri')]`, `#[Put('uri')]`, `#[Patch('uri')]`, `#[Delete('uri')]`, `#[Options('uri')]`, `#[Any('uri')]`
    - **Multiple verbs:** `#[Route(['put', 'patch'], 'uri')]`
    - **Route name:** `#[Get('uri', name: 'route.name')]`
    - **Middleware (method):** `#[Get('uri', middleware: MiddlewareClass::class)]`
    - **Middleware (class):** `#[Middleware(MiddlewareClass::class)]` — applies to all methods
    - **Prefix (class):** `#[Prefix('api/v1')]` — prefixes all routes in the class
    - **Domain (class):** `#[Domain('api.example.com')]`
    - **Domain from config:** `#[DomainFromConfig('domains.api')]`
    - **Group:** `#[Group(domain: 'api.example.com', prefix: 'v1')]` — can stack multiple
    - **Where constraints:** `#[Where('param', '[0-9]+')]`, `#[WhereNumber('id')]`, `#[WhereUuid('id')]`, `#[WhereAlpha('name')]`, `#[WhereAlphaNumeric('slug')]`, `#[WhereUlid('id')]`, `#[WhereIn('status', ['active', 'inactive'])]`
    - **Resource:** `#[Resource('orders')]` or `#[ApiResource('orders')]` — supports `except`, `only`, `names`, `parameters`, `shallow`
    - **Scope bindings:** `#[ScopeBindings]` — enables scoped model bindings on a method or class
- The package auto-discovers controllers in `app/Transport/Http/Controller/`. All routes are auto-prefixed with `api/` and use the `api` middleware group via `config/route-attributes.php`.
- Define the `api` middleware group in `bootstrap/app.php`:

```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->api([
        \Illuminate\Routing\Middleware\SubstituteBindings::class,
        \Illuminate\Routing\Middleware\ThrottleRequests::class . ':api',
    ]);
})
```

The `api` rate limiter is configured in `App\Infrastructure\Provider\RateLimitProvider`:

```php
<?php

declare(strict_types=1);

namespace App\Infrastructure\Provider;

use Illuminate\Cache\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\ServiceProvider;

final class RateLimitProvider extends ServiceProvider
{
    public function boot(RateLimiter $rateLimiter): void
    {
        $rateLimiter->for('api', fn (): Limit => Limit::perMinute(60));
    }
}
```

- Use validation attributes in FormRequest classes.

### Custom Artisan Generators

The project ships with a local package `package/laravel-generators` located at `packages/laravel-generators/`. It overrides default `make:*` commands so they generate classes into the Clean Architecture directories:

```bash
make:controller FooController   # app/Transport/Http/Controller/FooController.php
make:model Foo                  # app/Domain/Model/Foo.php
make:request FooRequest         # app/Transport/Http/Request/FooRequest.php
make:resource FooResource       # app/Transport/Http/Resource/FooResource.php
make:provider FooProvider       # app/Infrastructure/Provider/FooProvider.php
make:exception FooException     # app/Domain/Exception/FooException.php
make:enum FooEnum               # app/Domain/Enum/FooEnum.php
make:command FooCommand         # app/Infrastructure/Console/Command/FooCommand.php
make:service FooService         # app/Application/Service/FooService.php
make:action FooAction           # app/Application/Action/FooAction.php
make:doc FooDoc                 # app/Transport/Http/Doc/FooDoc.php
```

The package is registered as a local Composer path repository in `composer.json` and its `Package\LaravelGenerators\GeneratorServiceProvider` is added to `bootstrap/providers.php`.

Additional local modules can be placed under `modules/` and are auto-discovered by the `modules/*` path repository with symlinks enabled.

The target namespaces are configured in `packages/laravel-generators/config/generators.php`:

```php
'namespaces' => [
    'controller' => 'Transport\\Http\\Controller',
    'model' => 'Domain\\Model',
    'request' => 'Transport\\Http\\Request',
    'resource' => 'Transport\\Http\\Resource',
    'provider' => 'Infrastructure\\Provider',
    'exception' => 'Domain\\Exception',
    'enum' => 'Domain\\Enum',
    'command' => 'Infrastructure\\Console\\Command',
    'service' => 'Application\\Service',
    'action' => 'Application\\Action',
    'doc' => 'Transport\\Http\\Doc',
],
```

This keeps the application code clean and the generator logic isolated in `packages/`.

### Idiomatic Laravel (When Not Conflicting With Above)

- Prefer built-in Laravel features (collections, query scopes, event listeners) over custom logic.
- Use Laravel's built-in pagination via `paginate()` or `simplePaginate()` in Repositories.
- Use Laravel's built-in transaction handling — but inject `ConnectionInterface` into Services and call `$connection->transaction()`. Transactions belong in the **business logic layer (Services)**, NOT in Repositories. NEVER use the `DB` facade.
- Use Laravel's built-in queue system for async jobs — but dispatch STRICTLY via injected contracts (`Illuminate\Contracts\Queue\Queue` or `Illuminate\Bus\Dispatcher`). NEVER use the `Queue` or `Bus` facade. NEVER use the `dispatch()` global helper.

### Form Requests

- All validation logic MUST reside in dedicated `FormRequest` classes.
- FormRequest classes should use typed rules arrays and explicit authorization logic.

### API Resources

- All API responses MUST be handled via `JsonResource` or `JsonResource::collection`.
- Resources transform Dto objects (not Eloquent models directly) into JSON responses.

### Configuration

- All configuration MUST be done via `config/` files and environment variables.
- NEVER hardcode configuration values in application code.
- Use `env()` ONLY in `config/` files. Use `config()` ONLY in `config/` files or service providers.

## Service Provider Bindings

All Repository and Service interface bindings MUST be explicitly defined in `App\Infrastructure\Provider\BindingServiceProvider` (a dedicated provider for all interface-to-implementation bindings):

```php
<?php

declare(strict_types=1);

namespace App\Infrastructure\Provider;

use App\Domain\Repository\OrderRepositoryInterface;
use App\Infrastructure\Repository\OrderRepository;
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
        $this->app->bind(OrderRepositoryInterface::class, OrderRepository::class);
    }
}
```

Providers are registered in `bootstrap/providers.php`.
