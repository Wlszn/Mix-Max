<?php

namespace App\Domain\Models;

use App\Helpers\Core\PDOService;

class BookingTicketModel extends BaseModel
{
    /**
     * Find a booking ticket record by its ID
     */
    public function findById(int $id): array|false
    {
        return $this->selectOne(
            'SELECT * FROM booking_ticket WHERE bookingTicketId = ?',
            [$id]
        );
    }

    /**
     * Find all ticket records for a booking
     */
    public function findByBooking(int $bookingId): array
    {
        return $this->selectAll(
            'SELECT * FROM booking_ticket WHERE bookingId = ?',
            [$bookingId]
        );
    }

    /**
     * Find booking ticket record by ticket ID
     */
    public function findByTicket(int $ticketId): array|false
    {
        return $this->selectOne(
            'SELECT * FROM booking_ticket WHERE ticketId = ?',
            [$ticketId]
        );
    }

    /**
     * Find booking ticket with full details
     */
    public function findWithDetails(int $id): array|false
    {
        return $this->selectOne(
            'SELECT bt.*, b.bookingRef, b.date as bookingDate, b.status as bookingStatus,
                    t.section, t.rowLetter, t.seatNumber, t.price as ticketPrice,
                    e.title, e.artist, e.date as eventDate, e.startTime, e.endTime,
                    v.name as venueName, v.address, v.city
             FROM booking_ticket bt
             JOIN booking b ON bt.bookingId = b.bookingId
             JOIN ticket t ON bt.ticketId = t.ticketId
             JOIN event e ON t.eventId = e.eventId
             JOIN venue v ON e.venueId = v.venueId
             WHERE bt.bookingTicketId = ?',
            [$id]
        );
    }

    /**
     * Create a new booking ticket record
     * 
     * @param int $bookingId The booking ID
     * @param int $ticketId The ticket ID
     * @param float $pricePaid The price paid for this ticket
     * @return int The new booking ticket ID
     */
    public function create(int $bookingId, int $ticketId, float $pricePaid): int
    {
        $this->execute(
            'INSERT INTO booking_ticket (bookingId, ticketId, pricePaid) VALUES (?, ?, ?)',
            [$bookingId, $ticketId, $pricePaid]
        );
        
        return (int) $this->lastInsertId();
    }

    /**
     * Create multiple booking ticket records at once
     * 
     * @param int $bookingId The booking ID
     * @param array $tickets Array of ['ticketId' => int, 'pricePaid' => float]
     * @return array Array of new booking ticket IDs
     */
    public function createMultiple(int $bookingId, array $tickets): array
    {
        $ids = [];
        $this->beginTransaction();
        
        try {
            foreach ($tickets as $ticket) {
                $this->execute(
                    'INSERT INTO booking_ticket (bookingId, ticketId, pricePaid) VALUES (?, ?, ?)',
                    [$bookingId, $ticket['ticketId'], $ticket['pricePaid']]
                );
                $ids[] = (int) $this->lastInsertId();
            }
            $this->commit();
        } catch (\Exception $e) {
            $this->rollback();
            throw $e;
        }
        
        return $ids;
    }

    /**
     * Update the price paid for a ticket
     */
    public function updatePrice(int $id, float $pricePaid): bool
    {
        return $this->execute(
            'UPDATE booking_ticket SET pricePaid = ? WHERE bookingTicketId = ?',
            [$pricePaid, $id]
        ) > 0;
    }

    /**
     * Delete a booking ticket record
     */
    public function delete(int $id): bool
    {
        return $this->execute('DELETE FROM booking_ticket WHERE bookingTicketId = ?', [$id]) > 0;
    }

    /**
     * Delete all ticket records for a booking
     */
    public function deleteByBooking(int $bookingId): bool
    {
        return $this->execute('DELETE FROM booking_ticket WHERE bookingId = ?', [$bookingId]) > 0;
    }

    /**
     * Count tickets in a booking
     */
    public function countByBooking(int $bookingId): int
    {
        return $this->count('SELECT COUNT(*) FROM booking_ticket WHERE bookingId = ?', [$bookingId]);
    }

    /**
     * Get total price for a booking
     */
    public function getTotalPrice(int $bookingId): float
    {
        $result = $this->selectOne(
            'SELECT SUM(pricePaid) as total FROM booking_ticket WHERE bookingId = ?',
            [$bookingId]
        );
        
        return (float) ($result['total'] ?? 0);
    }

    /**
     * Check if a booking ticket exists
     */
    public function exists(int $id): bool
    {
        return $this->count('SELECT COUNT(*) FROM booking_ticket WHERE bookingTicketId = ?', [$id]) > 0;
    }

    /**
     * Check if a ticket is already in a booking
     */
    public function ticketInBooking(int $bookingId, int $ticketId): bool
    {
        return $this->count(
            'SELECT COUNT(*) FROM booking_ticket WHERE bookingId = ? AND ticketId = ?',
            [$bookingId, $ticketId]
        ) > 0;
    }

    /**
     * Get all bookings for a specific ticket
     */
    public function findBookingsForTicket(int $ticketId): array
    {
        return $this->selectAll(
            'SELECT b.* FROM booking b
             JOIN booking_ticket bt ON b.bookingId = bt.bookingId
             WHERE bt.ticketId = ?',
            [$ticketId]
        );
    }

    /**
     * Get booking summary with ticket count and total
     */
    public function getBookingSummary(int $bookingId): array|false
    {
        return $this->selectOne(
            'SELECT 
                COUNT(*) as ticketCount,
                SUM(pricePaid) as totalPrice,
                AVG(pricePaid) as avgPrice,
                MIN(pricePaid) as minPrice,
                MAX(pricePaid) as maxPrice
             FROM booking_ticket
             WHERE bookingId = ?',
            [$bookingId]
        );
    }
}