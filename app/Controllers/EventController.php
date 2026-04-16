<?php

namespace App\Controllers;

use App\Domain\Services\EventService;
use DI\Container;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class EventController extends BaseController
{
    private EventService $eventService;

    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->eventService = $container->get(EventService::class);
    }

    public function index(Request $request, Response $response): Response
    {
        $queryParams = $request->getQueryParams();
        $search = $queryParams['search'] ?? '';

        if (!empty($search)) {
            $events = $this->eventService->searchEvents($search);
        } else {
            $events = $this->eventService->getAllEvents();
        }

        return $this->render($response, 'events/index.php', [
            'page_title' => 'Events',
            'events' => $events,
            'search' => $search
        ]);
    }

    public function show(Request $request, Response $response, array $args): Response
    {
        $eventId = (int) $args['id'];
        $event = $this->eventService->getEventById($eventId);

        if (!$event) {
            $response->getBody()->write('Event not found');
            return $response->withStatus(404);
        }

        return $this->render($response, 'events/show.php', [
            'page_title' => $event['title'],
            'event' => $event
        ]);
    }
}