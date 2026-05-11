<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Domain\Services\BookingService;
use App\Domain\Services\CartService;
use App\Domain\Services\TicketService;
use App\Helpers\Core\PDOService;  // ADD THIS LINE
use DI\Container;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class CartController extends BaseController
{
    private CartService $cartService;
    private TicketService $ticketService;
    private BookingService $bookingService;

    public function __construct(Container $container)
    {
        parent::__construct($container);

        $this->cartService = new CartService();
        $this->ticketService = $container->get(TicketService::class);
        $this->bookingService = new BookingService($container->get(PDOService::class));  // Now this works
    }

    public function index(Request $request, Response $response): Response
    {
        return $this->render($response, 'cart/cart.php', [
            'page_title' => 'Cart',
            'cart' => $this->cartService->getCart()
        ]);
    }

    public function add(Request $request, Response $response): Response
    {
        $ticketIds = $request->getParsedBody()['ticketIds'] ?? [];

        if (is_array($ticketIds)) {
            foreach ($ticketIds as $ticketId) {
                $ticketId = (int) $ticketId;
                if ($ticketId > 0) {
                    $this->cartService->addTicketById($ticketId, $this->ticketService);
                }
            }
        } else {
            // Handle single ticketId for backward compatibility
            $ticketId = (int) (($request->getParsedBody()['ticketId'] ?? 0));
            if ($ticketId > 0) {
                $this->cartService->addTicketById($ticketId, $this->ticketService);
            }
        }

        return $this->redirect($request, $response, 'cart.index');
    }

    public function remove(Request $request, Response $response): Response
    {
        $ticketId = (int) (($request->getParsedBody()['ticketId'] ?? 0));

        if ($ticketId > 0) {
            $this->cartService->removeFromCart($ticketId);
        }

        return $this->redirect($request, $response, 'cart.index');
    }

    public function clear(Request $request, Response $response): Response
    {
        $this->cartService->clearCart();

        return $this->redirect($request, $response, 'cart.index');
    }

    public function buy(Request $request, Response $response): Response
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $userId = $_SESSION['user']['userId'] ?? null;
        if (!$userId) {
            return $this->redirect($request, $response, 'auth.login');
        }

        $ticketId = (int) (($request->getParsedBody()['ticketId'] ?? 0));
        if ($ticketId <= 0) {
            return $this->redirect($request, $response, 'cart.index');
        }

        $cart = $this->cartService->getCart();
        $selectedCart = array_filter($cart, fn($item) => $item['ticketId'] == $ticketId);
        if (empty($selectedCart)) {
            return $this->redirect($request, $response, 'cart.index');
        }

        $bookingId = $this->bookingService->createBooking($userId, array_values($selectedCart));
        if ($bookingId) {
            $this->cartService->removeFromCart($ticketId);
            // TODO: redirect to booking confirmation
        }

        return $this->redirect($request, $response, 'cart.index');
    }

    public function buySelected(Request $request, Response $response): Response
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $userId = $_SESSION['user']['userId'] ?? null;
        if (!$userId) {
            return $this->redirect($request, $response, 'auth.login');
        }

        $ticketIds = $request->getParsedBody()['ticketIds'] ?? [];
        if (!is_array($ticketIds) || empty($ticketIds)) {
            return $this->redirect($request, $response, 'cart.index');
        }

        $cart = $this->cartService->getCart();
        $selectedCart = array_filter($cart, fn($item) => in_array($item['ticketId'], $ticketIds));
        if (empty($selectedCart)) {
            return $this->redirect($request, $response, 'cart.index');
        }

        $bookingId = $this->bookingService->createBooking($userId, array_values($selectedCart));
        if ($bookingId) {
            foreach ($ticketIds as $id) {
                $this->cartService->removeFromCart((int)$id);
            }
            // TODO: redirect to booking confirmation
        }

        return $this->redirect($request, $response, 'cart.index');
    }
}