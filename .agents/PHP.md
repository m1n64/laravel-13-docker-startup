# PHP 8.5 Strict Rules

**Language:** PHP 8.5 (STRICTLY).

Reference: https://www.php.net/manual/en/migration85.php

## MANDATORY Directives

Every PHP file MUST start with:

```php
<?php

declare(strict_types=1);
```

## REQUIRED Features (Use These Always)

- **Native types:** Always use native PHP type hints for parameters and return types (`int`, `string`, `bool`, `array`, `float`, `void`, `never`).
- **Union/Intersection types:** Use `int|string`, `string&Countable` where applicable.
- **Nullable types:** Use `?string` or `string|null`.
- **Constructor property promotion:** Use promoted properties (`public function __construct(private string $name)`).
- **Readonly properties:** Use `readonly` for immutable properties (PHP 8.1+).
- **Readonly classes:** Use `readonly class` for fully immutable classes (PHP 8.2+).
- **Enums:** Use native `enum` (PHP 8.1+). NEVER emulate enums with classes and constants.
- **Match expressions:** Use `match` instead of `switch` where applicable.
- **Named arguments:** Allowed and encouraged for readability with multiple optional parameters.
- **Attributes:** Use PHP 8 attributes (`#[Attribute]`) instead of PHPDoc annotations for metadata.
- **Arrow functions:** Use `fn() =>` for simple closures.
- **Null coalescing assignment:** Use `??=`.
- **Spread operator:** Use `[...$arr]` in arrays and function arguments.
- **`never` return type:** Use for functions that never return (`exit`, `throw`, `redirect-and-die`).
- **First-class callable syntax:** Use `strlen(...)` instead of `fn($s) => strlen($s)` (PHP 8.1+).
- **Fibers:** Available. Use when implementing cooperative multitasking.

## FORBIDDEN

- **NO** emulation of enums via classes with constants. Use native `enum`.
- **NO** PHPDoc-only type definitions when native types are possible.
- **NO** `@` error suppression operator. Handle errors explicitly.
- **NO** `var_dump`, `print_r`, `dd` in production code.
- **NO** `extract`, `compact` for passing data to views.
- **NO** `eval` or `create_function`.
- **NO** dynamic class names via string concatenation for instantiation. Use a proper resolver or factory.

## Enum Example (PHP 8.5)

```php
<?php

declare(strict_types=1);

namespace App\Domain\Enum;

/**
 * Represents the status of a payment processing operation.
 */
enum PaymentStatusEnum: string
{
    case PENDING = 'pending';
    case COMPLETED = 'completed';
    case FAILED = 'failed';
    case REFUNDED = 'refunded';
}
```

## Strict Type Example

```php
<?php

declare(strict_types=1);

namespace App\Application\Service\Payment;

use App\Domain\Repository\PaymentGatewayInterface;
use App\Application\Dto\PaymentResultDto;
use App\Domain\Exception\PaymentGatewayException;

/**
 * Processes payment transactions through the configured gateway.
 */
final class PaymentService
{
    /**
     * @param PaymentGatewayInterface $gateway The payment gateway implementation.
     */
    public function __construct(
        private readonly PaymentGatewayInterface $gateway,
    ) {
    }

    /**
     * Processes a payment for the given order.
     *
     * @param int $orderId The order identifier.
     * @param float $amount The amount to charge.
     * @return PaymentResultDto The result of the payment operation.
     * @throws PaymentGatewayException If the gateway returns an error.
     */
    public function processPayment(int $orderId, float $amount): PaymentResultDto
    {
        if ($amount <= 0) {
            throw new PaymentGatewayException('Amount must be greater than zero.');
        }

        return $this->gateway->charge($orderId, $amount);
    }
}
```
