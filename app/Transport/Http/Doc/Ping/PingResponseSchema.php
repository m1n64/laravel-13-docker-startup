<?php

declare(strict_types=1);

namespace App\Transport\Http\Doc\Ping;

use OpenApi\Attributes as OA;

/**
 * OpenAPI schema for the ping response.
 */
#[OA\Schema(
    schema: 'PingResponse',
    description: 'Ping response payload.',
)]
final class PingResponseSchema
{
    #[OA\Property(type: 'string', example: 'pong')]
    public string $message;

    #[OA\Property(type: 'string', format: 'date-time', example: '2026-09-15T14:48:00+00:00')]
    public string $timestamp;
}
