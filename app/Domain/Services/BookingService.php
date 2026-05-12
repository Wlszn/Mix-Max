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

        // Generate a unique booking reference
        $bookingRef = 'BK-' . strtoupper(uniqid()) . '-' . rand(100, 999);
        
        // Prepare data as ASSOCIATIVE array (not indexed)
        $bookingData = [
            'userId' => $userId,
            'bookingRef' => $bookingRef,
            'date' => date('Y-m-d H:i:s'),
            'totalPrice' => $totalPrice
        ];

        // Create the booking
        $bookingId = $this->bookingModel->create($bookingData);

        if (!$bookingId) {
            return false;
        }

        // Create booking ticket records
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