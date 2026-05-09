<?php

declare(strict_types=1);

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
        return $this->render($response, 'cart/cart.php', [
            'page_title' => 'Cart',
            'cart' => $this->cartService->getCart()
        ]);
    }

    public function add(Request $request, Response $response): Response
    {
        $ticketId = (int) (($request->getParsedBody()['ticketId'] ?? 0));

        if ($ticketId > 0) {
            $this->cartService->addTicketById($ticketId, $this->ticketService);
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
}