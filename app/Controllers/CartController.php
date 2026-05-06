<?php

namespace App\Controllers;

use App\Domain\Services\CartService;
use DI\Container;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class CartController extends BaseController
{
    private CartService $cartService;

    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->cartService = new CartService();
    }

    public function index(Request $request, Response $response): Response
    {
        $cart = $this->cartService->getCart();

        return $this->render($response, 'cart/cart.php', [
            'page_title' => 'Cart',
            'cart' => $cart
        ]);
    }
}