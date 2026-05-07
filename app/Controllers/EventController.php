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
        $search = trim($queryParams['search'] ?? '');
        $category = $queryParams['category'] ?? '';
        $date = $queryParams['date'] ?? '';
        $sort = $queryParams['sort'] ?? 'ending_soon';

        $events = $this->eventService->filterEvents($search, $category, $date, $sort);

        return $this->render($response, 'events/browse.php', [
            'page_title' => 'Events',
            'events' => $events,
            'search' => $search,
            'category' => $category,
            'date' => $date,
            'sort' => $sort
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

        $tickets = $this->eventService->getTicketsByEvent($eventId);

        return $this->render($response, 'events/show.php', [
            'page_title' => $event['title'],
            'event' => $event,
            'tickets' => $tickets
        ]);
    }

    public function create(Request $request, Response $response): Response
    {
        return $this->render($response, 'events/create.php', [
            'page_title' => 'Create Event'
        ]);
    }

    public function store(Request $request, Response $response): Response
    {
        $data = (array) $request->getParsedBody();

        $userId = $_SESSION['user']['userId'] ?? null;

        if (!$userId) {
            return $this->redirect($request, $response, 'home.index');
        }

        $this->eventService->createUserEvent($data, (int) $userId);

        return $this->redirect($request, $response, 'events.index');
    }

    public function searchJson(Request $request, Response $response): Response
    {
        $queryParams = $request->getQueryParams();
        $keyword = $queryParams['q'] ?? '';

        $events = $this->eventService->liveSearchEvents($keyword);

        $payload = json_encode([
            'success' => true,
            'events' => $events
        ]);

        $response->getBody()->write($payload);

        return $response->withHeader('Content-Type', 'application/json');
    }
}