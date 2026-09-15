# Route Attributes (spatie/laravel-route-attributes)

This project uses `spatie/laravel-route-attributes` (https://github.com/spatie/laravel-route-attributes) for attribute-based routing. All routes MUST be defined using PHP 8 attributes directly on controller methods. NEVER use `routes/web.php` or `routes/api.php` for new routes.

The package auto-discovers controllers in `app/Transport/Http/Controller/` (configurable in `config/route-attributes.php`). All routes are auto-prefixed with `api/`.

## HTTP Verb Attributes

```php
use Spatie\RouteAttributes\Attributes\Get;
use Spatie\RouteAttributes\Attributes\Post;
use Spatie\RouteAttributes\Attributes\Put;
use Spatie\RouteAttributes\Attributes\Patch;
use Spatie\RouteAttributes\Attributes\Delete;
use Spatie\RouteAttributes\Attributes\Options;
use Spatie\RouteAttributes\Attributes\Any;

class OrderController
{
    #[Get('orders')]
    public function index() {}

    #[Post('orders')]
    public function store() {}

    #[Get('orders/{id}')]
    public function show(int $id) {}

    #[Put('orders/{id}')]
    public function update(int $id) {}

    #[Delete('orders/{id}')]
    public function destroy(int $id) {}
}
```

## Multiple Verbs on One Method

```php
use Spatie\RouteAttributes\Attributes\Route;

#[Route(['put', 'patch'], 'orders/{id}')]
public function update(int $id) {}
```

## Route Name

```php
#[Get('orders', name: 'orders.index')]
public function index() {}
```

## Middleware

**On a method:**

```php
#[Get('admin/dashboard', middleware: AuthMiddleware::class)]
public function dashboard() {}
```

**On a class (applies to all methods):**

```php
use Spatie\RouteAttributes\Attributes\Middleware;

#[Middleware(AuthMiddleware::class)]
class AdminController
{
    #[Get('admin/dashboard')]
    public function dashboard() {}

    #[Get('admin/settings', middleware: SettingsMiddleware::class)]
    public function settings() {}
}
```

Class-level and method-level middleware are merged. In the example above, `settings` gets both `AuthMiddleware` and `SettingsMiddleware`.

## Prefix (Class-Level)

```php
use Spatie\RouteAttributes\Attributes\Prefix;

#[Prefix('api/v1')]
class OrderController
{
    #[Get('orders')]        // -> GET api/v1/orders
    public function index() {}

    #[Post('orders')]       // -> POST api/v1/orders
    public function store() {}
}
```

## Domain (Class-Level)

```php
use Spatie\RouteAttributes\Attributes\Domain;

#[Domain('api.example.com')]
class OrderController
{
    #[Get('orders')]
    public function index() {}
}
```

## Domain From Config

```php
use Spatie\RouteAttributes\Attributes\DomainFromConfig;

#[DomainFromConfig('domains.api')]
class OrderController
{
    #[Get('orders')]
    public function index() {}
}
```

Reads the value from `config('domains.api')` and uses it as the domain.

## Group (Multiple Domain/Prefix Combos)

```php
use Spatie\RouteAttributes\Attributes\Group;

#[Group(domain: 'api.example.com', prefix: 'v1')]
#[Group(domain: 'api.example.com', prefix: 'v2')]
class OrderController
{
    #[Get('orders')]  // -> registered for both v1 and v2
    public function index() {}
}
```

## Where Constraints

```php
use Spatie\RouteAttributes\Attributes\Where;
use Spatie\RouteAttributes\Attributes\WhereNumber;
use Spatie\RouteAttributes\Attributes\WhereUuid;

#[WhereNumber('id')]
class OrderController
{
    #[Get('orders/{id}')]
    public function show(int $id) {}
}
```

Available `Where*` attributes:

- `#[Where('param', '[0-9]+')]` — custom regex
- `#[WhereAlpha('name')]` — alphabetic only
- `#[WhereAlphaNumeric('slug')]` — alphanumeric
- `#[WhereNumber('id')]` — numeric
- `#[WhereUuid('id')]` — UUID format
- `#[WhereUlid('id')]` — ULID format
- `#[WhereIn('status', ['active', 'inactive'])]` — enum-like

Where attributes can be applied on both classes and methods. Class-level applies to all methods.

## Resource Controllers

```php
use Spatie\RouteAttributes\Attributes\Resource;
use Spatie\RouteAttributes\Attributes\ApiResource;
use Spatie\RouteAttributes\Attributes\Prefix;

#[Prefix('api/v1')]
#[ApiResource('orders', except: ['destroy'])]
class OrderController
{
    // Registers: index, store, show, update (no destroy)
}
```

`#[Resource]` registers full resource routes. `#[ApiResource]` registers only API routes (no `create` or `edit`).

Supported parameters: `except`, `only`, `names`, `parameters`, `shallow`.

## Scope Bindings

```php
use Spatie\RouteAttributes\Attributes\ScopeBindings;

#[Get('users/{user}/posts/{post}')]
#[ScopeBindings]
public function getUserPost(User $user, Post $post)
{
    return $post;
}
```

Enables scoped model bindings (the child model is scoped to the parent). Can be applied on a class to enable for all methods. Pass `false` to disable: `#[ScopeBindings(false)]`.

## Configuration

Publish the config file:

```bash
php artisan vendor:publish --provider="Spatie\RouteAttributes\RouteAttributesServiceProvider" --tag="config"
```

Example `config/route-attributes.php`:

```php
return [
    'enabled' => true,
    'directories' => [
        app_path('Transport/Http/Controller') => [
            'prefix' => 'api',
            'middleware' => 'api',
        ],
    ],
];
```

The `api` middleware group is defined in `bootstrap/app.php` and contains `SubstituteBindings` and `ThrottleRequests:api`.

# OpenAPI Documentation (zircote/swagger-php)

This project uses `zircote/swagger-php` (https://github.com/zircote/swagger-php) for strict OpenAPI specification via PHP 8 attributes.

## Separation of Concerns

- **Controllers** (`app/Transport/Http/Controller/`) — MUST contain only routing attributes (`#[Get]`, `#[Post]`, etc.) and HTTP wiring. NO OpenAPI attributes in controllers.
- **Doc classes** (`app/Transport/Http/Doc/`) — contain OpenAPI attributes (`#[OA\Get]`, `#[OA\Post]`, `#[OA\Schema]`, `#[OA\Response]`, etc.). One doc class per controller or per logical group.
- **Dto classes** (`app/Application/Dto/`) — MAY contain `#[OA\Schema]` attributes, because they define the data shapes shared between layers.

This keeps the transport layer clean and the OpenAPI contract explicit and versionable.

## Directory Structure

```
app/Transport/Http/Doc/
├── Info.php
└── User/
    ├── ShowDoc.php
    └── UserSchema.php
```

## Info Class

```php
<?php

declare(strict_types=1);

namespace App\Transport\Http\Doc;

use OpenApi\Attributes as OA;

#[OA\OpenApi(openapi: '3.1.0')]
#[OA\Info(
    title: 'Laravel 13 API',
    version: '1.0.0',
    description: 'API documentation for the Laravel 13 Clean Architecture template.',
)]
final class Info
{
    private function __construct()
    {
    }
}
```

## Schema Class

```php
<?php

declare(strict_types=1);

namespace App\Transport\Http\Doc\User;

use OpenApi\Attributes as OA;

#[OA\Schema(
    title: 'User',
    description: 'A user in the system.',
    required: ['id', 'name', 'email'],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'John Doe'),
        new OA\Property(property: 'email', type: 'string', format: 'email', example: 'john@example.com'),
    ],
    type: 'object',
)]
final class UserSchema
{
    private function __construct()
    {
    }
}
```

## Operation Class

```php
<?php

declare(strict_types=1);

namespace App\Transport\Http\Doc\User;

use OpenApi\Attributes as OA;

final class ShowDoc
{
    private function __construct()
    {
    }

    #[OA\Get(
        path: '/api/users/{id}',
        summary: 'Get a user by ID',
        description: 'Returns a single user resource by its identifier.',
        tags: ['Users'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'The user identifier.',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer'),
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'User found.',
                content: new OA\JsonContent(ref: UserSchema::class),
            ),
            new OA\Response(response: 404, description: 'User not found.'),
        ],
    )]
    public function __invoke(): void
    {
    }
}
```

## Dto with Schema

```php
<?php

declare(strict_types=1);

namespace App\Application\Dto;

use OpenApi\Attributes as OA;

#[OA\Schema(
    title: 'PaymentResult',
    required: ['orderId', 'isSuccess'],
    properties: [
        new OA\Property(property: 'orderId', type: 'integer'),
        new OA\Property(property: 'isSuccess', type: 'boolean'),
        new OA\Property(property: 'gatewayTransactionId', type: 'string', nullable: true),
    ],
    type: 'object',
)]
final readonly class PaymentResultDto
{
    public function __construct(
        public int $orderId,
        public bool $isSuccess,
        public ?string $gatewayTransactionId = null,
    ) {
    }
}
```

## Generation

Generate the OpenAPI spec:

```bash
make docs
```

This runs:

```bash
vendor/bin/openapi \
    app/Transport/Http/Doc \
    app/Transport/Http/Controller \
    app/Application/Dto \
    modules/*/src/Transport/Http/Doc \
    modules/*/src/Transport/Http/Controller \
    modules/*/src/Application/Dto \
    -o public/api/openapi.json
```

`make docs` automatically includes matching module directories (if any modules exist).

The generated `public/api/openapi.json` is served as a static asset and consumed by the documentation UI.

## Viewing the Documentation

The documentation UI is served by the application at `GET /api/docs`. The route is defined by `App\Transport\Http\Controller\ApiDocsController`:

```php
<?php

declare(strict_types=1);

namespace App\Transport\Http\Controller;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Spatie\RouteAttributes\Attributes\Get;

/**
 * Serves the API documentation UI at /api/docs.
 */
final class ApiDocsController extends Controller
{
    /**
     * @param Factory $viewFactory The view factory.
     */
    public function __construct(
        private readonly Factory $viewFactory,
    ) {
    }

    /**
     * Renders the API documentation page.
     *
     * @return View The documentation view.
     */
    #[Get('docs', name: 'api.docs')]
    public function __invoke(): View
    {
        return $this->viewFactory->make('api.docs');
    }
}
```

The Blade template `resources/views/api/docs.blade.php` uses Stoplight Elements from a CDN:

```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Documentation</title>
    <link rel="stylesheet" href="https://unpkg.com/@stoplight/elements/styles.min.css">
</head>
<body>
    <elements-api
        apiDescriptionUrl="{{ asset('api/openapi.json') }}"
        router="hash"
        layout="sidebar"
    ></elements-api>

    <script src="https://unpkg.com/@stoplight/elements/web-components.min.js" crossorigin></script>
</body>
</html>
```

Access the docs at `http://localhost:<port>/api/docs`.
