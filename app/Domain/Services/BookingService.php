<?php

namespace App\Domain\Services;

use App\Domain\Models\BookingModel;
use App\Domain\Models\BookingTicketModel;
use App\Helpers\Core\PDOService;

class BookingService extends BaseService
{
    private BookingModel $bookingModel;
    private BookingTicketModel $bookingTicketModel;

    public function __construct(PDOService $db_service)
    {
        $this->bookingModel = new BookingModel($db_service);
        $this->bookingTicketModel = new BookingTicketModel($db_service);
    }

    public function createBooking(int $userId, array $cart): int|false
    {
        if (empty($cart)) {
            return false;
        }

        $totalPrice = 0;
        foreach ($cart as $item) {
            $totalPrice += (float)$item['price'];
        }

        $bookingId = $this->bookingModel->create([
            'userId'     => $userId,
            'bookingRef' => 'BK-' . strtoupper(bin2hex(random_bytes(8))),
            'date'       => date('Y-m-d'),
            'totalPrice' => $totalPrice,
        ]);

        if (!$bookingId) {
            return false;
        }

        foreach ($cart as $item) {
            $this->bookingTicketModel->create(
                $bookingId,
                (int)$item['ticketId'],
                (float)$item['price']
            );
        }

        return $bookingId;
    }
}