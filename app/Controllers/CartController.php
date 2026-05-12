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

    // Store selected tickets in session for payment page
    $_SESSION['payment_tickets'] = array_values($selectedCart);
    
    // Redirect to payment page
    return $this->redirect($request, $response, 'cart.payment');
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
    
    // If only one ticketId is sent as string, convert to array
    if (!is_array($ticketIds) && !empty($ticketIds)) {
        $ticketIds = [$ticketIds];
    }
    
    if (empty($ticketIds)) {
        return $this->redirect($request, $response, 'cart.index');
    }

    $cart = $this->cartService->getCart();
    $selectedCart = [];
    
    foreach ($cart as $item) {
        if (in_array($item['ticketId'], $ticketIds)) {
            $selectedCart[] = $item;
        }
    }
    
    if (empty($selectedCart)) {
        return $this->redirect($request, $response, 'cart.index');
    }

    $_SESSION['payment_tickets'] = $selectedCart;
    
    return $this->redirect($request, $response, 'cart.payment');
}

public function payment(Request $request, Response $response): Response
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Get tickets from session
    $tickets = $_SESSION['payment_tickets'] ?? [];
    
    if (empty($tickets)) {
        // No tickets selected, redirect to cart
        return $this->redirect($request, $response, 'cart.index');
    }
    
    $totalPrice = array_sum(array_column($tickets, 'price'));
    
    return $this->render($response, 'cart/payment.php', [
        'page_title' => 'Payment',
        'tickets' => $tickets,
        'totalPrice' => $totalPrice
    ]);
}
}