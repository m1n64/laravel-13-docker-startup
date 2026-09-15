# Modules

This project supports optional local modules located in `modules/`. Each module is a self-contained, Clean-Architecture package with its own `composer.json` and PSR-4 autoloading.

## CRITICAL: Do Not Auto-Create Modules

**Agents must NEVER create a new module unless the user explicitly asks for it.** If the user's request does not contain a direct instruction to create a module, add only one. Do not infer the need for a module from vague feature descriptions.

## What Is a Module

A module is a **full Composer-like package** with:

- A dedicated `modules/<ModuleName>/` directory.
- Its own `composer.json` with `name`, `version`, and PSR-4 autoload.
- Root namespace `Module\<ModuleName>`.
- The same four-layer internal structure as `app/`:
  - `Domain/`
  - `Application/`
  - `Infrastructure/`
  - `Transport/`
- A public `Boundary/` that exposes the module's use cases through facades and contracts.

## Module Structure

```
modules/Payment/
├── composer.json
├── src/
│   ├── Boundary/
│   │   ├── PaymentFacade.php
│   │   └── PaymentFacadeInterface.php
│   ├── Domain/
│   │   ├── Model/
│   │   ├── Repository/
│   │   ├── Enum/
│   │   └── Exception/
│   ├── Application/
│   │   ├── Service/
│   │   ├── Action/
│   │   └── Dto/
│   ├── Infrastructure/
│   │   ├── Repository/
│   │   ├── Provider/
│   │   └── External/
│   └── Transport/
│       └── Http/
│           ├── Controller/
│           ├── Doc/
│           ├── Request/
│           └── Resource/
```

## composer.json Example

```json
{
    "name": "module/payment",
    "description": "Payment processing module.",
    "type": "library",
    "license": "MIT",
    "version": "1.0.0",
    "require": {
        "php": "^8.5"
    },
    "autoload": {
        "psr-4": {
            "Module\\Payment\\": "src/"
        }
    },
    "minimum-stability": "stable",
    "prefer-stable": true
}
```

The module is auto-discovered by the root `composer.json` `modules/*` path repository with symlinks enabled.

## Boundary and Facades

A module's public API is exposed through a **Boundary** layer.

### Rules

- The `Boundary/` namespace lives at `Module\<Name>\Boundary\`.
- Every facade must implement a **contract** (interface).
- Consumers of a module must depend on the contract, never the concrete facade.
- Facades are injected through the constructor. **No static access. No Laravel-style Facades.**
- A facade orchestrates the module's internal Application/Domain services and returns DTOs.

### Example

```php
<?php

declare(strict_types=1);

namespace Module\Payment\Boundary;

use Module\Payment\Application\Dto\PaymentResultDto;

/**
 * Public contract for the Payment module.
 */
interface PaymentFacadeInterface
{
    /**
     * Charges the given order.
     *
     * @param int $orderId The order identifier.
     * @param float $amount The amount to charge.
     * @return PaymentResultDto The payment result.
     */
    public function charge(int $orderId, float $amount): PaymentResultDto;
}
```

```php
<?php

declare(strict_types=1);

namespace Module\Payment\Boundary;

use Module\Payment\Application\Dto\PaymentResultDto;
use Module\Payment\Application\Service\PaymentService;

/**
 * Entry point for the Payment module.
 */
final class PaymentFacade implements PaymentFacadeInterface
{
    /**
     * @param PaymentService $paymentService The payment use-case service.
     */
    public function __construct(
        private readonly PaymentService $paymentService,
    ) {
    }

    public function charge(int $orderId, float $amount): PaymentResultDto
    {
        return $this->paymentService->charge($orderId, $amount);
    }
}
```

The module's service provider binds the contract to the implementation:

```php
$this->app->bind(
    PaymentFacadeInterface::class,
    PaymentFacade::class,
);
```

## Module Registration

If the module has a Laravel service provider (e.g. `Module\Payment\Infrastructure\Provider\PaymentServiceProvider`), register it in `bootstrap/providers.php`:

```php
return [
    // ...
    \Module\Payment\Infrastructure\Provider\PaymentServiceProvider::class,
];
```

The main `config/route-attributes.php` already scans `modules/*/src/Transport/Http/Controller` and registers module controllers with the same `api/` prefix and `api` middleware group as the core controllers. No extra configuration is needed as long as the module follows the standard structure.

The `make docs` command also scans `modules/*/src/Transport/Http/Doc` and `modules/*/src/Application/Dto` for OpenAPI attributes, so module endpoints and DTOs are included in `public/api/openapi.json` automatically.

## Cross-Module and Core Communication

- The main `app/` core may consume module facades by injecting their `Boundary` interfaces.
- Modules may NOT depend on the main `App\` core. A module can only depend on other modules' `Boundary` contracts.
- Prefer events over direct `Boundary` calls when a module needs to notify others without creating tight coupling.

## Naming Conventions

- Module directory: PascalCase, singular (`Payment`, `Reporting`, `Inventory`).
- Root namespace: `Module\<ModuleName>`.
- Internal layers: same rules as `app/` (Domain/Application/Infrastructure/Transport, all singular).
- Facade class: `<ModuleName>Facade`.
- Facade contract: `<ModuleName>FacadeInterface`.
