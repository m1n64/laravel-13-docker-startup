<?php

declare(strict_types=1);

namespace App\Transport\Http\Doc\Ping;

use OpenApi\Attributes as OA;

/**
 * OpenAPI documentation for GET /api/ping.
 */
#[OA\Get(
    path: '/api/ping',
    operationId: 'ping',
    description: 'Health check endpoint.',
    tags: ['System'],
)]
#[OA\Response(
    response: 200,
    description: 'Pong response',
    content: new OA\JsonContent(ref: '#/components/schemas/PingResponse'),
)]
final class PingDoc
{
    private function __construct()
    {
    }
}
