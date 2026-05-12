<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Domain\Services\EventService;
use App\Domain\Services\TicketService;
use DI\Container;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class EventController extends BaseController
{
    private EventService $eventService;
    private TicketService $ticketService;

    public function __construct(Container $container)
    {
        parent::__construct($container);

        $this->eventService = $container->get(EventService::class);
        $this->ticketService = $container->get(TicketService::class);
    }

    public function index(Request $request, Response $response): Response
    {
        $filters = $this->getEventFilters($request);
        $events = $this->eventService->filterEvents(
            $filters['search'],
            $filters['category'],
            $filters['date'],
            $filters['sort']
        );

        return $this->render($response, 'events/browse.php', [
            'page_title' => 'Events',
            'events' => $events,
            ...$filters
        ]);
    }

    public function show(Request $request, Response $response, array $args): Response
    {
        $eventId = (int) $args['id'];
        $event = $this->eventService->getEventById($eventId);

        if (!$event) {
            return $this->render($response->withStatus(404), 'errors/404.php');
        }

        return $this->render($response, 'events/show.php', [
            'page_title' => $event['title'],
            'event' => $event,
            'tickets' => $this->ticketService->getTicketsByEvent($eventId),
            'similarEvents' => $this->eventService->getSimilarEvents(
                $eventId,
                $event['category'] ?? '',
                $event['city'] ?? ''
            )
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
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $userId = $_SESSION['user']['userId'] ?? null;

        if (!$userId) {
            return $this->redirect($request, $response, 'auth.login');
        }

        try {
            $this->eventService->createUserEvent(
                (array) $request->getParsedBody(),
                $_FILES,
                (int) $userId
            );
        } catch (\Throwable $e) {
            echo '<pre>';
            echo $e->getMessage();
            echo "\n\n";
            echo $e->getTraceAsString();
            echo '</pre>';
            exit;
        }

        $_SESSION['flash_success'] = 'Your event was submitted for review. It will appear once an admin approves it.';

        return $this->redirect($request, $response, 'home.index');
    }

    public function searchJson(Request $request, Response $response): Response
    {
        $keyword = $request->getQueryParams()['q'] ?? '';
        $events = $this->eventService->liveSearchEvents($keyword);

        $response->getBody()->write(json_encode([
            'success' => true,
            'events' => $events
        ]));

        return $response->withHeader('Content-Type', 'application/json');
    }

    private function getEventFilters(Request $request): array
    {
        $queryParams = $request->getQueryParams();

        return [
            'search' => trim($queryParams['search'] ?? ''),
            'category' => $queryParams['category'] ?? '',
            'date' => $queryParams['date'] ?? '',
            'sort' => $queryParams['sort'] ?? 'ending_soon'
        ];
    }

    public function adminPending(Request $request, Response $response): Response
    {
        $events = $this->eventService->getAllEventsForAdmin();

        return $this->render($response, 'admin/events-manage.php', [
            'page_title' => 'Manage Events',
            'events' => $events
        ]);
    }

    public function approve(Request $request, Response $response, array $args): Response
    {
        $eventId = (int) $args['id'];

        $this->eventService->updateEventStatus($eventId, 'scheduled');

        return $this->redirect($request, $response, 'admin.events');
    }

    public function reject(Request $request, Response $response, array $args): Response
    {
        $eventId = (int) $args['id'];

        $this->eventService->updateEventStatus($eventId, 'rejected');

        return $this->redirect($request, $response, 'admin.events');
    }

    public function adminDashboard(Request $request, Response $response): Response
    {
        $dashboardData = $this->eventService->getAdminDashboardData();

        return $this->render($response, 'admin/dashboard.php', [
            'page_title' => 'Admin Dashboard',
            ...$dashboardData
        ]);
    }

    public function adminManageEvents(Request $request, Response $response): Response
    {
        $events = $this->eventService->getAllEventsForAdmin();

        return $this->render($response, 'admin/events-manage.php', [
            'page_title' => 'Manage Events',
            'events' => $events
        ]);
    }

    public function adminDeleteEvent(Request $request, Response $response, array $args): Response
    {
        $this->eventService->deleteEvent((int) $args['id']);

        return $this->redirect($request, $response, 'admin.events.manage');
    }
}