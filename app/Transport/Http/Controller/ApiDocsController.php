<?php

declare(strict_types=1);

namespace App\Transport\Http\Controller;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Spatie\RouteAttributes\Attributes\Get;

/**
 * Serves the API documentation UI at /api/docs.
 */
final class ApiDocsController extends Controller
{
    /**
     * @param Factory $viewFactory The view factory.
     */
    public function __construct(
        private readonly Factory $viewFactory,
    ) {
    }

    /**
     * Renders the API documentation page.
     *
     * @return View The documentation view.
     */
    #[Get('docs', name: 'api.docs')]
    public function __invoke(): View
    {
        return $this->viewFactory->make('api.docs');
    }
}
