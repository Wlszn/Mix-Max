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
    
    $totalPrice = array_sum(array_column($tickets, 'price'));
    $paymentMethodId = $request->getParsedBody()['paymentMethodId'] ?? null;
    
    if (!$paymentMethodId) {
        $_SESSION['flash_error'] = 'Invalid payment method';
        return $this->redirect($request, $response, 'cart.payment');
    }
    
    // Get Stripe secret key
    $stripeConfig = $this->container->get(AppSettings::class)->get('stripe');
    \Stripe\Stripe::setApiKey($stripeConfig['secret_key']);
    
    try {
        // Create a PaymentIntent
        $paymentIntent = \Stripe\PaymentIntent::create([
            'amount' => (int)($totalPrice * 100),
            'currency' => 'usd',
            'payment_method' => $paymentMethodId,
            'confirmation_method' => 'manual',
            'confirm' => true,
            'metadata' => [
                'user_id' => $userId,
                'ticket_ids' => implode(',', array_column($tickets, 'ticketId'))
            ]
        ]);
        
        if ($paymentIntent->status === 'succeeded') {
            // Create booking
            $bookingId = $this->bookingService->createBooking($userId, $tickets);
            
            if ($bookingId) {
                // Set success flash message
                $_SESSION['flash_success'] = '✅ Payment successful! Your booking has been confirmed. Booking ID: #' . $bookingId;
                
                // Clear the session data
                unset($_SESSION['payment_tickets']);
                $this->cartService->clearCart();
                
                // Redirect to home page with success message
                return $this->redirect($request, $response, 'home.index');
            }
        }
        
        // Payment failed
        $_SESSION['flash_error'] = 'Payment failed. Please try again.';
        return $this->redirect($request, $response, 'cart.payment');
        
    } catch (\Exception $e) {
        error_log("Payment error: " . $e->getMessage());
        $_SESSION['flash_error'] = 'Payment error: ' . $e->getMessage();
        return $this->redirect($request, $response, 'cart.payment');
    }
}

    public function paymentResult(Request $request, Response $response): Response
    {
        $status = $request->getQueryParams()['status'] ?? 'unknown';
        $bookingId = $request->getQueryParams()['booking_id'] ?? null;
        $errorMessage = $request->getQueryParams()['message'] ?? null;
        
        return $this->render($response, 'cart/result.php', [
            'page_title' => 'Payment Result',
            'status' => $status,
            'bookingId' => $bookingId,
            'errorMessage' => $errorMessage
        ]);
    }
}