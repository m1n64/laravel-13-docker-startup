<?php

declare(strict_types=1);

namespace App\Transport\Http\Controller;

use App\Application\Service\PingService;
use App\Transport\Http\Resource\PingResource;
use Spatie\RouteAttributes\Attributes\Get;

/**
 * Health check controller.
 */
final class PingController extends Controller
{
    /**
     * Initializes a new instance of the PingController class.
     *
     * @param PingService $pingService The ping service.
     */
    public function __construct(
        private readonly PingService $pingService,
    ) {
    }

    /**
     * Returns a ping response.
     *
     * @return PingResource The ping response resource.
     */
    #[Get('ping', name: 'api.ping')]
    public function __invoke(): PingResource
    {
        return new PingResource($this->pingService->pong());
    }
}
