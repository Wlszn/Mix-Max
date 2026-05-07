<?php

namespace App\Domain\Services;
use App\Helpers\Core\PDOService;

class CartService extends BaseService
{

    public function __construct()
    {
        // Initialize any dependencies or services here
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
    }

    public function addToCart(array $ticket): void
{
    foreach ($_SESSION['cart'] as $item) {
        if ((int)$item['ticketId'] === (int)$ticket['ticketId']) {
            return;
        }
    }

    $_SESSION['cart'][] = $ticket;
}

    public function getCart(): array
    {
        return $_SESSION['cart'];
    }

    public function removeFromCart(int $ticketId): void
    {
        $_SESSION['cart'] = array_values(array_filter(
            $_SESSION['cart'],
            fn($item) => $item['ticketId'] != $ticketId
        ));
    }

    public function clearCart(): void
    {
        $_SESSION['cart'] = [];
    }

    public function getTotal(): float
    {
        $total = 0;

        foreach ($_SESSION['cart'] as $item) {
            $total += (float)$item['price'];
        }

        return $total;
    }

}