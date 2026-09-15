<?php

declare(strict_types=1);

namespace App\Transport\Http\Doc;

use OpenApi\Attributes as OA;

/**
 * OpenAPI metadata for the application.
 */
#[OA\OpenApi(openapi: '3.0.3')]
#[OA\Info(
    title: 'Laravel 13 API',
    version: '1.0.0',
    description: 'API documentation for the Laravel 13 Clean Architecture template.',
)]
#[OA\Server(
    url: 'http://localhost',
    description: 'Local development server',
)]
final class Info
{
    private function __construct()
    {
    }
}
