<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Application\Dto\PingResponseDto;
use DateTimeImmutable;
use DateTimeInterface;

/**
 * Returns a simple ping response with a timestamp.
 */
final class PingService
{
    /**
     * Returns a ping response.
     *
     * @return PingResponseDto The ping response DTO.
     */
    public function pong(): PingResponseDto
    {
        $now = new DateTimeImmutable();

        return new PingResponseDto(
            'pong',
            $now->format(DateTimeInterface::ATOM),
        );
    }
}
