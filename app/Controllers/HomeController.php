<?php

declare(strict_types=1);

namespace App\Controllers;

use DI\Container;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Domain\Services\EventService;

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
        $allEvents = $this->eventService->getAllEvents();
        $featuredEvents = array_slice($allEvents, 0, 3);

        $data = [
            'page_title' => 'Mix Max - Discover Amazing Events',
            'featuredEvents' => $featuredEvents
        ];

        return $this->render($response, 'homeView.php', $data);
    }

    public function error(Request $request, Response $response, array $args = []): Response
    {
        return $this->render($response, 'errorView.php');
    }
}