<?php

namespace App\Controllers;

use App\Domain\Services\CartService;
use App\Domain\Services\TicketService;
use DI\Container;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class CartController extends BaseController
{
    private CartService $cartService;
    private TicketService $ticketService;

    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->cartService = new CartService();
        $this->ticketService = $container->get(TicketService::class);
    }

    public function index(Request $request, Response $response): Response
    {
        $cart = $this->cartService->getCart();

        return $this->render($response, 'cart/cart.php', [
            'page_title' => 'Cart',
            'cart' => $cart
        ]);
    }

    public function add(Request $request, Response $response): Response
{
    $data = $request->getParsedBody();
    $ticketId = (int)($data['ticketId'] ?? 0);

    if ($ticketId <= 0) {
        return $this->redirect($request, $response, 'cart.index');
    }

    $ticket = $this->ticketService->getTicketById($ticketId);

    if (!$ticket) {
        return $this->redirect($request, $response, 'cart.index');
    }

    if (!$this->ticketService->isTicketAvailable($ticketId)) {
        return $this->redirect($request, $response, 'cart.index');
    }

    $this->cartService->addToCart($ticket);

    return $this->redirect($request, $response, 'cart.index');
}

}