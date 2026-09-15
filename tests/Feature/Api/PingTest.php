<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use Tests\TestCase;

/**
 * Feature tests for the ping endpoint.
 */
final class PingTest extends TestCase
{
    /**
     * The ping endpoint returns a pong message and a timestamp.
     */
    public function test_ping_returns_pong_and_timestamp(): void
    {
        $response = $this->getJson('/api/ping');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'timestamp',
            ])
            ->assertJsonFragment([
                'message' => 'pong',
            ]);

        $this->assertNotEmpty($response->json('timestamp'));
    }
}
