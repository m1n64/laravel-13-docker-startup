# Strict Architecture & Layered Pattern Manifest

## Technology Stack

- **Language:** PHP 8.5 (STRICTLY).
- **Framework:** Laravel 13.x — https://laravel.com/docs/13.x
- **Database:** PostgreSQL 18+.
- **Cache / Queue:** Redis 7+.
- **Web Server:** Caddy 2+.
- **Containerization:** Docker + Docker Compose.

See also: [PHP.md](PHP.md) | [LARAVEL.md](LARAVEL.md) | [API.md](API.md) | [NAMING.md](NAMING.md) | [SECURITY.md](SECURITY.md) | [TESTING.md](TESTING.md)

## Directory Structure (Clean Architecture)

```
app/
├── Domain/
│   ├── Model/           # Eloquent models (App\Domain\Model)
│   ├── Repository/      # Repository interfaces / contracts (App\Domain\Repository)
│   ├── Enum/            # Domain enums (App\Domain\Enum)
│   └── Exception/       # Domain exceptions (App\Domain\Exception)
├── Application/
│   ├── Service/         # Use case services (App\Application\Service)
│   ├── Action/          # Single-purpose action classes (App\Application\Action)
│   └── Dto/             # Data transfer objects (App\Application\Dto)
├── Infrastructure/
│   ├── Repository/      # Repository implementations (App\Infrastructure\Repository)
│   ├── Provider/        # Service providers (App\Infrastructure\Provider)
│   └── External/       # External API clients, adapters (App\Infrastructure\External)
├── Transport/
│   └── Http/
│       ├── Controller/  # HTTP controllers (App\Transport\Http\Controller)
│       ├── Doc/         # OpenAPI documentation (App\Transport\Http\Doc)
│       ├── Request/     # Form requests (App\Transport\Http\Request)
│       └── Resource/    # API resources / transformers (App\Transport\Http\Resource)
```

### Dependency Flow

```
Transport (Controller) → Application (Service) → Domain (Repository Interface)
                                                        ↑
                                                  Infrastructure (Repository) implements
```

- **Domain** does not depend on any other layer.
- **Application** depends on Domain (interfaces only).
- **Infrastructure** implements Domain interfaces.
- **Transport** depends on Application (Services, Dto).
- All directory names are SINGULAR (Model, Repository, Service, Provider, Controller, Doc, Request, Resource).
- Every directory under `app/` contains a `.gitkeep` file so the full skeleton is committed to version control even when a layer is empty.
- **When you create the first real file in a directory that only contains `.gitkeep`, you MUST delete the `.gitkeep` file. Do not leave it alongside real files.**

## 1. Core Principles

### Strict Typing

Every PHP file MUST start with `declare(strict_types=1);`.

### PSR & PER Compliance

Strictly adhere to PSR-1, PSR-4, PSR-12, and PER (PHP Evolution Recommendations).

### SOLID

- **SRP (Single Responsibility Principle):** A class/method does one thing. Controllers do not contain business logic (move it to services).
- **OCP (Open/Closed Principle):** Use interfaces and composition to extend functionality without modifying existing code.
- **LSP (Liskov Substitution Principle):** Subclasses must be substitutable for their base classes.
- **ISP (Interface Segregation Principle):** Many specialized interfaces are better than one universal interface.
- **DIP (Dependency Inversion Principle):**
    - Depend on abstractions (interfaces), not implementations.
    - Use **strict constructor injection** for all dependencies.
    - All dependencies (Repositories, Services, Infrastructure, Clients, etc.) MUST be injected via the constructor.
    - NEVER use `app()`, `resolve()`, `auth()`, `request()` or any other global helpers inside Controllers, Services, Repositories, or Actions.
    - All components must be designed for full testability via constructor injection.

### DRY (Don't Repeat Yourself)

Do not duplicate code. Extract shared logic into:
- Base abstract classes.
- Traits (for cross-cutting concerns only).
- Dedicated services or utilities.

### KISS (Keep It Simple, Stupid)

Avoid over-abstraction and over-engineering. Write simple, readable, and maintainable code.

## 2. Strict Dependency Injection (CRITICAL)

All dependencies MUST be injected via the constructor. This is non-negotiable.

- NEVER use Laravel Facades (`DB::`, `Cache::`, `Config::`, `Log::`, etc.) in any layer. See [LARAVEL.md](LARAVEL.md) for the full facade-to-interface mapping.
- NEVER use global helper functions (`app()`, `resolve()`, `auth()`, `request()`, `config()`, `env()`, etc.) in application code.
- `env()` is ONLY allowed in `config/` files. NEVER in application code.
- `config()` is ONLY allowed in `config/` files or service providers. In application code, inject `Illuminate\Contracts\Config\Repository`.
- Use `readonly` properties for all injected dependencies.
- Bind all Repository and Service interfaces to implementations in `App\Infrastructure\Provider\BindingServiceProvider`.

## 3. Dto & Data Flow

All data passing between layers (Controller -> Service -> Repository -> Service -> Resource) MUST be encapsulated in immutable Dto classes.

- Dto classes prevent leaking Eloquent models into the business logic layer.
- Dto classes are immutable: use `readonly` properties and constructor promotion.
- Naming: MUST use `*Dto` suffix (e.g., `PaymentResultDto`).
- Dto classes MUST NOT contain business logic or validation. They are pure data structures.

### Example Dto

```php
<?php

declare(strict_types=1);

namespace App\Application\Dto;

/**
 * Represents the result of a payment processing operation.
 */
final readonly class PaymentResultDto
{
    /**
     * @param int $orderId The order identifier.
     * @param bool $isSuccess Whether the payment was successful.
     * @param string|null $gatewayTransactionId The transaction ID from the gateway, if available.
     */
    public function __construct(
        public int $orderId,
        public bool $isSuccess,
        public ?string $gatewayTransactionId = null,
    ) {
    }
}
```

## 4. Application Layering & Strict Routing

### Controllers (Super Skinny)

- **Responsibilities:** Validate input via FormRequest, map to Dto, call Service/Action, return JsonResource.
- **NO business logic, NO database queries, NO Eloquent calls.**
- **NO DIRECT REPOSITORY CALLS:** Controllers MUST NEVER interact with Repositories. Interaction is allowed ONLY via Services.
- Controllers MUST NOT contain middleware definitions (no `__construct` middleware or `$this->middleware()`). All middleware MUST be applied via route attributes (`#[Middleware]`).
- Controllers MUST be thin: receive a FormRequest, create a Dto, pass it to a Service, return a Resource.

### Example Controller

```php
<?php

declare(strict_types=1);

namespace App\Transport\Http\Controller;

use App\Application\Dto\CreateOrderDto;
use App\Application\Service\Order\OrderService;
use App\Transport\Http\Request\CreateOrderRequest;
use App\Transport\Http\Resource\OrderResource;
use Illuminate\Http\JsonResponse;
use Spatie\RouteAttributes\Attributes\Post;

/**
 * Handles HTTP requests related to orders.
 */
final class OrderController extends Controller
{
    /**
     * @param OrderService $orderService The order service.
     */
    public function __construct(
        private readonly OrderService $orderService,
    ) {
    }

    /**
     * Creates a new order.
     *
     * @param CreateOrderRequest $request The validated request.
     * @return JsonResponse The JSON response with the created order.
     */
    #[Post('orders')]
    public function store(CreateOrderRequest $request): JsonResponse
    {
        $dto = CreateOrderDto::fromRequest($request->validated());
        $order = $this->orderService->create($dto);

        return (new OrderResource($order))
            ->response()
            ->setStatusCode(201);
    }
}
```

### Strict Response Handling

- Usage of `response()->json([...])` or helper `response()` is STRICTLY FORBIDDEN.
- All API responses MUST be handled via `JsonResource` or `JsonResource::collection`.

### Form Requests

- All validation logic MUST reside in dedicated `FormRequest` classes.
- FormRequest classes use typed rules arrays and explicit authorization logic.
- NEVER use `$request->all()`. Always use `$request->validated()`.

### Business Logic (Services & Actions)

- Business logic is entirely contained within Services and Actions.
- Services handle use cases and orchestrate Repository calls.
- Actions are single-purpose classes for specific operations (e.g., `CreateOrderAction`).
- Services receive Dto objects, not Eloquent models or raw arrays.
- Services return Dto objects or domain objects, not Eloquent models.

### Repository Pattern

- Repositories MUST implement Interfaces (Contracts) in `App\Domain\Repository`.
- Services/Actions depend only on these Interfaces via constructor injection.
- Eloquent models are strictly for data representation within Repositories.
- Repositories MAY return Eloquent models, Dto objects, or simple typed values — choose the simplest approach that keeps the Service layer clean. Mapping to Dto is encouraged when crossing domain boundaries, but is not mandatory for every call.
- Repositories are the ONLY layer allowed to interact with Eloquent and the database.

### Example Repository Interface

```php
<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Application\Dto\CreateOrderDto;
use App\Application\Dto\OrderDto;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Contract for order data persistence operations.
 */
interface OrderRepositoryInterface
{
    /**
     * Finds an order by its identifier.
     *
     * @param int $id The order identifier.
     * @return OrderDto The order data transfer object.
     * @throws ModelNotFoundException If the order does not exist.
     */
    public function findById(int $id): OrderDto;

    /**
     * Creates a new order.
     *
     * @param CreateOrderDto $dto The order creation data.
     * @return OrderDto The created order data transfer object.
     */
    public function create(CreateOrderDto $dto): OrderDto;
}
```

## 5. Strict Infrastructure & Decoupling

### Facades Banned

The use of global Laravel Facades (e.g., `DB::`, `Cache::`, `Request::`, `Config::`, `Log::`) is STRICTLY FORBIDDEN in any layer (Services, Actions, Repositories, Controllers). Inject PSR-compliant interfaces or specific utility classes via the constructor instead. See [LARAVEL.md](LARAVEL.md) for the full mapping.

### Eloquent Isolation

Eloquent models are FORBIDDEN in Services and Actions. All data manipulation MUST happen within Repository methods. Services receive and return Dto objects.

### Transactions

Transactions belong in the **business logic layer (Services)**, NOT in Repositories. Services orchestrate transactional boundaries across one or more Repository calls.

- Inject `Illuminate\Database\ConnectionInterface` into Services and use `$connection->transaction()` — NEVER use the `DB` facade.
- Repositories MUST NOT open or manage transactions. They execute single queries within the transaction context provided by the Service.
- All data mutations (create, update, delete) that span multiple operations MUST be wrapped in a transaction at the Service level.

### Service Providers

All Repository and Service interface bindings MUST be explicitly defined in `App\Infrastructure\Provider\BindingServiceProvider` (a dedicated provider for all interface-to-implementation bindings).

## 6. Exceptions

- Use **custom domain exceptions** instead of generic `\Exception`, `\RuntimeException`, `\InvalidArgumentException`.
- Create a hierarchy of exceptions for the domain.
- Each exception MUST have a clear message and, when necessary, context (error codes, data).
- Exceptions live in `App\Domain\Exception\` or `App\Domain\Exception\<Domain>\`.

### Example Domain Exceptions

```php
<?php

declare(strict_types=1);

namespace App\Domain\Exception\Payment;

/**
 * Base exception for payment-related errors.
 */
abstract class PaymentException extends \RuntimeException
{
}

/**
 * Thrown when the payment gateway returns an error.
 */
final class PaymentGatewayException extends PaymentException
{
    /**
     * @param string $message The error message.
     * @param string|null $gatewayErrorCode The error code from the gateway, if available.
     * @param \Throwable|null $previous The previous exception, if any.
     */
    public function __construct(
        string $message,
        private readonly ?string $gatewayErrorCode = null,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, 0, $previous);
    }

    /**
     * Gets the error code returned by the payment gateway.
     *
     * @return string|null The gateway error code, or null if not provided.
     */
    public function getGatewayErrorCode(): ?string
    {
        return $this->gatewayErrorCode;
    }
}
```

## 7. Attributes Over Magic

- Use PHP 8 attributes for metadata instead of PHPDoc annotations where Laravel supports them.
- **Route attributes are MANDATORY.** This project uses `spatie/laravel-route-attributes` (https://github.com/spatie/laravel-route-attributes) for attribute-based routing. All routes MUST be defined using attributes (`#[Get]`, `#[Post]`, `#[Put]`, `#[Patch]`, `#[Delete]`, `#[Middleware]`, `#[Prefix]`, etc.) directly on controller methods. NEVER use `routes/web.php` or `routes/api.php` for new routes. See [API.md](API.md) for the full attribute reference and examples.
- All routes are auto-prefixed with `api/` via `config/route-attributes.php` and use the `api` middleware group (`SubstituteBindings`, `ThrottleRequests:api`). Controllers are auto-discovered in `app/Transport/Http/Controller/`.
- OpenAPI documentation lives in `app/Transport/Http/Doc/` using `zircote/swagger-php` attributes. Controllers MUST NOT contain OpenAPI attributes — they are reserved for doc classes. See [API.md](API.md) for the full OpenAPI reference and examples.
- Use validation attributes in FormRequest classes.
- Avoid Laravel's "magic" methods and auto-resolution where explicit configuration is possible.

## 8. Naming & PSR Standards

See [NAMING.md](NAMING.md) for the full naming conventions and coding standards.

Key rules:
- **Standardized Suffixes (Case-Sensitive):** Dto: `*Dto`, Enum: `*Enum`, Trait: `*Trait`, Contract: `*Interface`, Service: `*Service`, Action: `*Action`, Repository: `*Repository`, Request: `*Request`, Resource: `*Resource`, Exception: `*Exception`.
- **Standardized Prefixes:** Abstract classes: `Abstract*` (e.g., `AbstractPaymentService`).
- **Singular Directory Names (CRITICAL):** All directory names MUST be singular: `Model`, `Repository`, `Service`, `Provider`, `Controller`, `Doc`, `Request`, `Resource`, `Dto`, `Action`, `Enum`, `Exception`.
- **PSR-4 Namespacing (CRITICAL):** The namespace MUST strictly mirror the directory structure starting from the `app/` directory.

## 9. Execution Protocol

### JSON-Only Responses

All error responses MUST be returned as JSON, regardless of the request path or `Accept` header. This is configured in `bootstrap/app.php` via `shouldRenderJsonWhen(fn () => true)`.

### Idiomatic Laravel

Always prefer built-in Laravel features (collections, query scopes, event listeners) over custom logic — as long as they do not violate the strict DI and no-facade rules.

### Accountability

If a proposed solution violates any of these rules (including injecting dependencies via constructor, no facades, no global helpers), the agent is obligated to point it out and provide a refactored version conforming to this manifest.

### Testing Protocol (CRITICAL)

See [TESTING.md](TESTING.md) for the full testing protocol.

**Unit tests are MANDATORY.** Every Service, Action, and Repository method with business logic MUST have unit tests in `tests/Unit/`.

All new API endpoints MUST be strictly covered by functional (feature) tests. Every endpoint requires:
1. Success scenario (201/200).
2. Validation error scenario (422).
3. Authentication/Authorization scenario (401/403).

Production code will be rejected if it lacks corresponding tests in `tests/Unit/` and `tests/Feature/`.

**After every code iteration, the agent MUST run `make test` and ensure all tests pass.**
