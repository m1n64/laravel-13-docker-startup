# Coding Standards & Naming Conventions

## Mandatory Directives

Every PHP file MUST start with:

```php
<?php

declare(strict_types=1);
```

## PSR-12 and PER

- Strictly follow the PSR-12 formatting standard.
- Follow modern PER (PHP Evolution Recommendations) practices available in PHP 8.5.

## Naming

### Class Suffixes

The following suffixes are MANDATORY for the corresponding class types:

- **Enums:** `Enum` (e.g., `OrderStatusEnum`, `PaymentTypeEnum`)
- **Data Transfer Objects:** `Dto` (e.g., `PaymentDto`, `OrderDetailsDto`)
- **Services:** `Service` (e.g., `PaymentService`, `OrderProcessingService`)
- **Actions:** `Action` (e.g., `CreateOrderAction`, `RefundPaymentAction`)
- **Repositories:** `Repository` (e.g., `OrderRepository`, `PaymentRepository`)
- **Contracts / Interfaces:** `Interface` (e.g., `PaymentGatewayInterface`, `OrderRepositoryInterface`)
- **Controllers:** `Controller` (e.g., `PaymentController`, `OrderController`)
- **Form Requests:** `Request` (e.g., `CreateOrderRequest`, `RefundPaymentRequest`)
- **API Resources:** `Resource` (e.g., `OrderResource`, `PaymentCollectionResource`)
- **Exceptions:** `Exception` (e.g., `PaymentGatewayException`, `InvalidOrderStatusException`)
- **Traits:** `Trait` (e.g., `LoggableTrait`, `TimestampableTrait`)

### Class Prefixes

The following prefixes are MANDATORY for the corresponding class types:

- **Abstract classes:** `Abstract` (e.g., `AbstractPaymentService`, `AbstractBaseRepository`)
- **Interfaces:** No prefix (use `*Interface` suffix instead, e.g., `OrderRepositoryInterface`)

### Strict Suffix Enforcement

Any deviation from the casing above is PROHIBITED. For example, `DTO` instead of `Dto` is not allowed.

### Variable and Method Naming

- **Classes / Interfaces:** `PascalCase` (e.g., `PaymentService`, `OrderRepositoryInterface`)
- **Methods / Properties:** `camelCase` (e.g., `calculateTotal`, `orderStatus`)
- **Constants:** `UPPER_SNAKE_CASE` (e.g., `MAX_RETRY_COUNT`)
- **Database tables:** `snake_case` (e.g., `payment_orders`)
- **Database columns:** `snake_case` (e.g., `created_at`, `order_total`)
- **Boolean variables:** Prefix with `is`, `has`, `can`, or `should` (e.g., `$isActive`, `$hasPermission`)

### Intention-Revealing Names

Names MUST clearly state the purpose of the entity.

- FORBIDDEN: `$data`, `$arr`, `$tmp`, `$doStuff()`
- REQUIRED: `$userProfileData`, `$activeSubscriptions`, `$calculateMonthlyRevenue()`

## Namespaces

### Structure

Namespaces follow PSR-4 and MUST mirror the directory structure starting from the `app/` directory.

**Base structure:**
```
App\<Layer>\<Sublayer>\<Domain>
```

**Examples:**
```php
// Enums
namespace App\Domain\Enum;

// Models (Eloquent)
namespace App\Domain\Model;

// Repository interfaces
namespace App\Domain\Repository;

// Exceptions
namespace App\Domain\Exception;

// DTOs
namespace App\Application\Dto;

// Services
namespace App\Application\Service\Payment;

// Actions
namespace App\Application\Action;

// Repository implementations
namespace App\Infrastructure\Repository;

// Service providers
namespace App\Infrastructure\Provider;

// External clients
namespace App\Infrastructure\External;

// Controllers
namespace App\Transport\Http\Controller;

// OpenAPI documentation
namespace App\Transport\Http\Doc;

// Form Requests
namespace App\Transport\Http\Request;

// API Resources
namespace App\Transport\Http\Resource;
```

### Namespace-to-Directory Mapping

```
app/
├── Domain/
│   ├── Model/
│   │   └── User.php
│   ├── Repository/
│   │   └── OrderRepositoryInterface.php
│   ├── Enum/
│   │   └── OrderStatusEnum.php
│   └── Exception/
│       └── PaymentGatewayException.php
├── Application/
│   ├── Dto/
│   │   └── PaymentDto.php
│   ├── Service/
│   │   └── Payment/
│   │       └── PaymentService.php
│   └── Action/
│       └── CreateOrderAction.php
├── Infrastructure/
│   ├── Repository/
│   │   └── OrderRepository.php
│   ├── Provider/
│   │   ├── AppServiceProvider.php
│   │   └── BindingServiceProvider.php
│   └── External/
│       └── PaymentGatewayClient.php
├── Transport/
│   └── Http/
│       ├── Controller/
│       │   └── PaymentController.php
│       ├── Doc/
│       │   └── Payment/
│       │       └── PaymentShowDoc.php
│       ├── Request/
│       │   └── CreateOrderRequest.php
│       └── Resource/
│           └── OrderResource.php
```

### Skeleton

All directories under `app/` contain a `.gitkeep` file. This preserves the Clean Architecture skeleton in Git even when a layer has no classes yet.

**Rule:** When you add the first real file to a directory, you MUST delete the `.gitkeep` file. Do not leave `.gitkeep` alongside real files.

## PHPDoc

**Absolute documentation rule:** Every class, interface, trait, property, and method MUST have a PHPDoc block.

### Mandatory Elements

- **Classes, interfaces, traits:** Description of the class purpose.
- **Properties:** `@var type [description]`.
- **Methods:** `@param`, `@return`, `@throws` where applicable. Describe the *purpose* of the method, not just a repetition of its name.
- **Constructors:** MUST be documented even if they have no dependencies.
- **Empty constructors / trivial getters / setters:** MUST have PHPDoc.

### Example

```php
/**
 * Calculates the total price of items in the cart with applied discounts.
 *
 * @param list<CartItem> $items The list of items in the cart.
 * @param float $discountRate The discount rate to apply (e.g., 0.1 for 10%).
 * @return float The final calculated total price.
 * @throws InvalidDiscountException If the discount rate is negative or greater than 1.
 */
public function calculateTotal(array $items, float $discountRate): float
{
    // ...
}
```

## Pre-Output Checklist

Before generating or modifying any code, verify:

- [ ] `declare(strict_types=1);` at the top of the file?
- [ ] Code uses PHP 8.5 features (native types, enums, readonly, match, attributes)?
- [ ] PSR-12 compliance?
- [ ] SOLID, DRY, KISS principles applied?
- [ ] Strict constructor DI (no facades, no global helpers)?
- [ ] Custom domain exceptions (not generic `\Exception`)?
- [ ] Correct class suffixes (`Enum`, `Dto`, `Service`, `Action`, `Repository`, `Interface`, `Request`, `Resource`, `Exception`, `Trait`)?
- [ ] Correct class prefixes (`Abstract` for abstract classes)?
- [ ] Namespace mirrors the directory structure (PSR-4)?
- [ ] Layered architecture followed (Controller → Service → Repository)?
- [ ] Repositories implement interfaces from `App\Domain\Repository`?
- [ ] All database queries use parameterized binding?
- [ ] All data mutations wrapped in transactions?
- [ ] PHPDoc blocks on every class, method, and property?
- [ ] Tests written or updated for the changed business logic?
