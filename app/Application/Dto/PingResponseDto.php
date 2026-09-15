<?php

declare(strict_types=1);

namespace App\Application\Dto;

/**
 * Data transfer object for the ping response.
 */
final readonly class PingResponseDto
{
    /**
     * @param string $message The response message.
     * @param string $timestamp The response timestamp in ISO 8601 format.
     */
    public function __construct(
        public string $message,
        public string $timestamp,
    ) {
    }
}
