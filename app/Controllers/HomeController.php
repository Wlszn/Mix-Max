<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Domain\Services\EventService;
use DI\Container;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class HomeController extends BaseController
{
    private EventService $eventService;

    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->eventService = $container->get(EventService::class);
    }

    public function index(Request $request, Response $response, array $args = []): Response
    {
        return $this->render($response, 'home.php', [
            'page_title' => 'Mix Max - Discover Amazing Events',
            'featuredEvents' => $this->eventService->getFeaturedEvents(3)
        ]);
    }
}