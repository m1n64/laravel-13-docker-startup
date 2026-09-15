<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use Tests\TestCase;

/**
 * Tests for the API documentation route.
 */
class ApiDocsTest extends TestCase
{
    /**
     * Tests that the API docs route returns the Stoplight Elements UI.
     */
    public function test_api_docs_route_returns_documentation_ui(): void
    {
        $response = $this->get('/api/docs');

        $response->assertOk();
        $response->assertSee('elements-api', false);
        $response->assertSee('api/openapi.json', false);
    }
}
