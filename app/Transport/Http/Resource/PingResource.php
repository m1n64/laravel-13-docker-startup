<?php

declare(strict_types=1);

namespace App\Transport\Http\Resource;

use App\Application\Dto\PingResponseDto;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * JSON resource for a ping response.
 *
 * @mixin PingResponseDto
 */
final class PingResource extends JsonResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = null;

    /**
     * Transform the resource into an array.
     *
     * @param Request $request The current request.
     * @return array<string, string>
     */
    public function toArray($request): array
    {
        return [
            'message' => $this->resource->message,
            'timestamp' => $this->resource->timestamp,
        ];
    }
}
