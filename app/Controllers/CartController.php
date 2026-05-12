<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Domain\Services\BookingService;
use App\Domain\Services\CartService;
use App\Domain\Services\TicketService;
use App\Helpers\Core\PDOService;
use App\Helpers\Core\AppSettings;
use DI\Container;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Stripe\Stripe;

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
        $this->bookingService = new BookingService($container->get(PDOService::class));
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
        return $this->redirect($request, $response, 'cart.index');
    }
    
    $totalPrice = array_sum(array_column($tickets, 'price'));
    
    // Get Stripe public key from config
    $stripeConfig = $this->container->get(AppSettings::class)->get('stripe');
    $stripePublicKey = $stripeConfig['public_key'] ?? '';
    
    return $this->render($response, 'cart/payment.php', [
        'page_title' => 'Payment',
        'tickets' => $tickets,
        'totalPrice' => $totalPrice,
        'stripePublicKey' => $stripePublicKey  // Make sure this is passed
    ]);
}

    public function processPayment(Request $request, Response $response): Response
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $userId = $_SESSION['user']['userId'] ?? null;
        if (!$userId) {
            return $this->redirect($request, $response, 'auth.login');
        }

        $tickets = $_SESSION['payment_tickets'] ?? [];
        if (empty($tickets)) {
            return $this->redirect($request, $response, 'cart.index');
        }

        $stripeConfig = $this->container->get(AppSettings::class)->get('stripe');
        if (empty($stripeConfig['secret_key'])) {
            $_SESSION['flash_error'] = 'Stripe is not configured (missing secret key).';
            return $this->redirect($request, $response, 'cart.payment');
        }
        \Stripe\Stripe::setApiKey($stripeConfig['secret_key']);

        $uri      = $request->getUri();
        $basePath = defined('APP_ROOT_DIR_NAME') && APP_ROOT_DIR_NAME !== '' ? '/' . APP_ROOT_DIR_NAME : '';
        $origin   = $uri->getScheme() . '://' . $uri->getAuthority() . $basePath;

        $lineItems = [];
        foreach ($tickets as $t) {
            $lineItems[] = [
                'price_data' => [
                    'currency'     => 'usd',
                    'unit_amount'  => (int) round(((float) ($t['price'] ?? 0)) * 100),
                    'product_data' => [
                        'name'        => 'Ticket #' . ($t['ticketId'] ?? '?'),
                        'description' => 'Section ' . ($t['section'] ?? '-') . ', Row ' . ($t['rowLetter'] ?? '-') . ', Seat ' . ($t['seatNumber'] ?? '-'),
                    ],
                ],
                'quantity' => 1,
            ];
        }

        try {
            $session = \Stripe\Checkout\Session::create([
                'mode'                 => 'payment',
                'payment_method_types' => ['card'],
                'line_items'           => $lineItems,
                'success_url'          => $origin . '/payment/result?status=success&session_id={CHECKOUT_SESSION_ID}',
                'cancel_url'           => $origin . '/payment/result?status=cancel',
                'metadata'             => [
                    'user_id'    => $userId,
                    'ticket_ids' => implode(',', array_column($tickets, 'ticketId')),
                ],
            ]);
        } catch (\Exception $e) {
            error_log('Stripe Checkout error: ' . $e->getMessage());
            $_SESSION['flash_error'] = 'Could not start payment: ' . $e->getMessage();
            return $this->redirect($request, $response, 'cart.payment');
        }

        return $response
            ->withHeader('Location', $session->url)
            ->withStatus(303);
    }

    public function paymentResult(Request $request, Response $response): Response
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $query     = $request->getQueryParams();
        $status    = $query['status'] ?? 'unknown';
        $sessionId = $query['session_id'] ?? null;

        if ($status === 'cancel') {
            $_SESSION['flash_error'] = 'Payment was canceled.';
            return $this->redirect($request, $response, 'cart.index');
        }

        if ($status !== 'success' || !$sessionId) {
            $_SESSION['flash_error'] = 'Unknown payment status.';
            return $this->redirect($request, $response, 'cart.index');
        }

        $stripeConfig = $this->container->get(AppSettings::class)->get('stripe');
        \Stripe\Stripe::setApiKey($stripeConfig['secret_key']);

        try {
            $session = \Stripe\Checkout\Session::retrieve($sessionId);
        } catch (\Exception $e) {
            error_log('Stripe session retrieve error: ' . $e->getMessage());
            $_SESSION['flash_error'] = 'Could not verify payment.';
            return $this->redirect($request, $response, 'cart.index');
        }

        if (($session->payment_status ?? '') !== 'paid') {
            $_SESSION['flash_error'] = 'Payment was not completed (status: ' . ($session->payment_status ?? 'unknown') . ').';
            return $this->redirect($request, $response, 'cart.index');
        }

        $userId  = $_SESSION['user']['userId'] ?? (int) ($session->metadata->user_id ?? 0);
        $tickets = $_SESSION['payment_tickets'] ?? [];

        if (!$userId || empty($tickets)) {
            $_SESSION['flash_error'] = 'Payment succeeded but session expired before booking could be created. Contact support with reference ' . $session->id;
            return $this->redirect($request, $response, 'cart.index');
        }

        $bookingId = $this->bookingService->createBooking($userId, $tickets);

        if (!$bookingId) {
            $_SESSION['flash_error'] = 'Payment succeeded but booking could not be created. Contact support with reference ' . $session->id;
            return $this->redirect($request, $response, 'cart.index');
        }

        $_SESSION['flash_success'] = '✅ Payment successful! Your booking has been confirmed. Booking ID: #' . $bookingId;
        unset($_SESSION['payment_tickets']);
        $this->cartService->clearCart();
        return $this->redirect($request, $response, 'home.index');
    }
}