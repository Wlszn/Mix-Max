<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Domain\Models\BookingModel;
use App\Domain\Models\EventModel;
use App\Domain\Models\TicketModel;
use App\Domain\Models\UserModel;
use App\Domain\Models\VenueModel;
use App\Helpers\Core\PDOService;
use DI\Container;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AdminController extends BaseController
{
    private UserModel   $userModel;
    private EventModel  $eventModel;
    private TicketModel $ticketModel;
    private VenueModel  $venueModel;
    private BookingModel $bookingModel;

    public function __construct(Container $container)
    {
        parent::__construct($container);

        $db = $container->get(PDOService::class);
        $this->userModel    = new UserModel($db);
        $this->eventModel   = new EventModel($db);
        $this->ticketModel  = new TicketModel($db);
        $this->venueModel   = new VenueModel($db);
        $this->bookingModel = new BookingModel($db);

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // ── Guard ─────────────────────────────────────────────────────────────────

    private function requireAdmin(Request $request, Response $response): ?Response
    {
        if (empty($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'admin') {
            return $this->redirect($request, $response, 'home.index');
        }
        return null;
    }

    // ── Dashboard ─────────────────────────────────────────────────────────────

    public function dashboard(Request $request, Response $response): Response
    {
        if ($guard = $this->requireAdmin($request, $response)) return $guard;

        $stats = [
            'users'    => $this->userModel->countAll(),
            'events'   => $this->eventModel->countAll(),
            'tickets'  => $this->ticketModel->countAll(),
            'bookings' => $this->bookingModel->countAll(),
            'revenue'  => $this->bookingModel->getTotalRevenue(),
            'pending_events' => $this->eventModel->countByStatus('pending'),
        ];

        return $this->render($response, 'admin/dashboard.php', [
            'page_title'    => 'Admin Dashboard — Mix Max',
            'stats'         => $stats,
            'recent_users'  => $this->userModel->findRecent(5),
            'recent_events' => $this->eventModel->findRecent(5),
        ]);
    }

    // ── Users ─────────────────────────────────────────────────────────────────

    public function users(Request $request, Response $response): Response
    {
        if ($guard = $this->requireAdmin($request, $response)) return $guard;

        $search = trim($request->getQueryParams()['search'] ?? '');
        $users  = $search ? $this->userModel->search($search) : $this->userModel->findAll();

        return $this->render($response, 'admin/users.php', [
            'page_title' => 'Manage Users — Admin',
            'users'      => $users,
            'search'     => $search,
            'success'    => $_SESSION['flash_success'] ?? '',
        ]) ;
    }

    public function promoteUser(Request $request, Response $response, array $args): Response
    {
        if ($guard = $this->requireAdmin($request, $response)) return $guard;

        $userId = (int) $args['id'];
        $this->userModel->promoteUser($userId);
        $_SESSION['flash_success'] = 'User promoted to admin.';
        return $this->redirect($request, $response, 'admin.users');
    }

    public function demoteUser(Request $request, Response $response, array $args): Response
    {
        if ($guard = $this->requireAdmin($request, $response)) return $guard;

        $userId = (int) $args['id'];
        // Prevent self-demotion
        if ($userId === (int)$_SESSION['user']['userId']) {
            $_SESSION['flash_success'] = 'You cannot demote yourself.';
            return $this->redirect($request, $response, 'admin.users');
        }
        $this->userModel->demoteUser($userId);
        $_SESSION['flash_success'] = 'User demoted to regular user.';
        return $this->redirect($request, $response, 'admin.users');
    }

    public function deleteUser(Request $request, Response $response, array $args): Response
    {
        if ($guard = $this->requireAdmin($request, $response)) return $guard;

        $userId = (int) $args['id'];
        if ($userId === (int)$_SESSION['user']['userId']) {
            $_SESSION['flash_success'] = 'You cannot delete yourself.';
            return $this->redirect($request, $response, 'admin.users');
        }
        $this->userModel->delete($userId);
        $_SESSION['flash_success'] = 'User deleted.';
        return $this->redirect($request, $response, 'admin.users');
    }

    // ── Events ────────────────────────────────────────────────────────────────

    public function events(Request $request, Response $response): Response
    {
        if ($guard = $this->requireAdmin($request, $response)) return $guard;

        $search = trim($request->getQueryParams()['search'] ?? '');
        $status = trim($request->getQueryParams()['status'] ?? '');
        $events = $this->eventModel->findAllForAdmin($search, $status);

        return $this->render($response, 'admin/events.php', [
            'page_title' => 'Manage Events — Admin',
            'events'     => $events,
            'search'     => $search,
            'status'     => $status,
            'success'    => $_SESSION['flash_success'] ?? '',
        ]);
    }

    public function approveEvent(Request $request, Response $response, array $args): Response
    {
        if ($guard = $this->requireAdmin($request, $response)) return $guard;

        $this->eventModel->updateStatus((int)$args['id'], 'scheduled');
        $_SESSION['flash_success'] = 'Event approved.';
        return $this->redirect($request, $response, 'admin.events');
    }

    public function rejectEvent(Request $request, Response $response, array $args): Response
    {
        if ($guard = $this->requireAdmin($request, $response)) return $guard;

        $this->eventModel->updateStatus((int)$args['id'], 'rejected');
        $_SESSION['flash_success'] = 'Event rejected.';
        return $this->redirect($request, $response, 'admin.events');
    }

    public function deleteEvent(Request $request, Response $response, array $args): Response
    {
        if ($guard = $this->requireAdmin($request, $response)) return $guard;

        $this->eventModel->delete((int)$args['id']);
        $_SESSION['flash_success'] = 'Event deleted.';
        return $this->redirect($request, $response, 'admin.events');
    }

    // ── Tickets ───────────────────────────────────────────────────────────────

    public function tickets(Request $request, Response $response): Response
    {
        if ($guard = $this->requireAdmin($request, $response)) return $guard;

        $eventId = (int)($request->getQueryParams()['event'] ?? 0);
        $tickets = $eventId
            ? $this->ticketModel->findByEventWithDetails($eventId)
            : $this->ticketModel->findAllWithDetails();
        $events  = $this->eventModel->findAll();

        return $this->render($response, 'admin/tickets.php', [
            'page_title'     => 'Manage Tickets — Admin',
            'tickets'        => $tickets,
            'events'         => $events,
            'selected_event' => $eventId,
            'success'        => $_SESSION['flash_success'] ?? '',
        ]);
    }

    public function deleteTicket(Request $request, Response $response, array $args): Response
    {
        if ($guard = $this->requireAdmin($request, $response)) return $guard;

        $this->ticketModel->delete((int)$args['id']);
        $_SESSION['flash_success'] = 'Ticket deleted.';
        return $this->redirect($request, $response, 'admin.tickets');
    }

    // ── Bookings ──────────────────────────────────────────────────────────────

    public function bookings(Request $request, Response $response): Response
    {
        if ($guard = $this->requireAdmin($request, $response)) return $guard;

        $bookings = $this->bookingModel->findAllWithDetails();

        return $this->render($response, 'admin/bookings.php', [
            'page_title' => 'Manage Bookings — Admin',
            'bookings'   => $bookings,
            'success'    => $_SESSION['flash_success'] ?? '',
        ]);
    }
}