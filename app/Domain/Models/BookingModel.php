<?php

namespace App\Domain\Models;

use App\Helpers\Core\PDOService;

class BookingModel extends BaseModel
{
    /**
     * Find a booking by its ID
     */
    public function findById(int $id): array|false
    {
        return $this->selectOne(
            'SELECT * FROM booking WHERE bookingId = ?',
            [$id]
        );
    }

    /**
     * Find bookings by user ID
     */
    public function findByUser(int $userId): array
    {
        return $this->selectAll(
            'SELECT * FROM booking WHERE userId = ? ORDER BY date DESC, created_at DESC',
            [$userId]
        );
    }

    /**
     * Find bookings by status
     */
    public function findByStatus(string $status): array
    {
        return $this->selectAll(
            'SELECT * FROM booking WHERE status = ? ORDER BY date DESC, created_at DESC',
            [$status]
        );
    }

    /**
     * Find bookings by user and status
     */
    public function findByUserAndStatus(int $userId, string $status): array
    {
        return $this->selectAll(
            'SELECT * FROM booking WHERE userId = ? AND status = ? ORDER BY date DESC, created_at DESC',
            [$userId, $status]
        );
    }

    /**
     * Find bookings by date range
     */
    public function findByDateRange(string $startDate, string $endDate): array
    {
        return $this->selectAll(
            'SELECT * FROM booking WHERE date BETWEEN ? AND ? ORDER BY date DESC, created_at DESC',
            [$startDate, $endDate]
        );
    }

    /**
     * Find booking by reference number
     */
    public function findByRef(string $bookingRef): array|false
    {
        return $this->selectOne(
            'SELECT * FROM booking WHERE bookingRef = ?',
            [$bookingRef]
        );
    }

    /**
     * Create a new booking
     * 
     * @param array $data Booking data including: userId, bookingRef, date, totalPrice
     * @return int The new booking ID
     */
    public function create(array $data): int
    {
        $this->execute(
            'INSERT INTO booking (userId, bookingRef, date, totalPrice) VALUES (?, ?, ?, ?)',
            [
                $data['userId'],
                $data['bookingRef'],
                $data['date'],
                $data['totalPrice']
            ]
        );
        
        return (int) $this->lastInsertId();
    }

    /**
     * Update a booking
     */
    public function update(int $id, array $data): bool
    {
        return $this->execute(
            'UPDATE booking SET 
                userId = ?, 
                bookingRef = ?, 
                date = ?, 
                totalPrice = ? 
             WHERE bookingId = ?',
            [
                $data['userId'],
                $data['bookingRef'],
                $data['date'],
                $data['totalPrice'],
                $id
            ]
        ) > 0;
    }

    /**
     * Update booking status
     */
    public function updateStatus(int $id, string $status): bool
    {
        return $this->execute(
            'UPDATE booking SET status = ? WHERE bookingId = ?',
            [$status, $id]
        ) > 0;
    }

    /**
     * Confirm a booking (change status to confirmed)
     */
    public function confirm(int $id): bool
    {
        return $this->updateStatus($id, 'confirmed');
    }

    /**
     * Cancel a booking (change status to cancelled)
     */
    public function cancel(int $id): bool
    {
        return $this->updateStatus($id, 'cancelled');
    }

    /**
     * Complete a booking (change status to completed)
     */
    public function complete(int $id): bool
    {
        return $this->updateStatus($id, 'completed');
    }

    /**
     * Delete a booking
     */
    public function delete(int $id): bool
    {
        return $this->execute('DELETE FROM booking WHERE bookingId = ?', [$id]) > 0;
    }

    /**
     * Get booking with user and tickets details
     */
    public function findWithDetails(int $id): array|false
    {
        return $this->selectOne(
            'SELECT b.*, u.username, u.email 
             FROM booking b
             JOIN users u ON b.userId = u.userId
             WHERE b.bookingId = ?',
            [$id]
        );
    }

    /**
     * Get booking with all related tickets
     */
    public function findWithTickets(int $id): array|false
    {
        $booking = $this->findWithDetails($id);
        
        if (!$booking) {
            return false;
        }
        
        $booking['tickets'] = $this->getBookingTickets($id);
        
        return $booking;
    }

    /**
     * Get tickets for a booking
     */
    public function getBookingTickets(int $bookingId): array
    {
        return $this->selectAll(
            'SELECT t.*, bt.pricePaid 
             FROM ticket t
             JOIN booking_ticket bt ON t.ticketId = bt.ticketId
             JOIN booking b ON bt.bookingId = b.bookingId
             WHERE b.bookingId = ?',
            [$bookingId]
        );
    }

    /**
     * Count total bookings
     */
    public function countAll(): int
    {
        return $this->count('SELECT COUNT(*) FROM booking');
    }

    /**
     * Count bookings by status
     */
    public function countByStatus(string $status): int
    {
        return $this->count('SELECT COUNT(*) FROM booking WHERE status = ?', [$status]);
    }

    /**
     * Count bookings by user
     */
    public function countByUser(int $userId): int
    {
        return $this->count('SELECT COUNT(*) FROM booking WHERE userId = ?', [$userId]);
    }

    /**
     * Get total revenue from confirmed/completed bookings
     */
    public function getTotalRevenue(): float
    {
        $result = $this->selectOne(
            'SELECT SUM(totalPrice) as revenue FROM booking WHERE status IN ("confirmed", "completed")'
        );
        
        return (float) ($result['revenue'] ?? 0);
    }

    /**
     * Get recent bookings with limit
     */
    public function findRecent(int $limit = 10): array
    {
        return $this->selectAll(
            'SELECT * FROM booking ORDER BY created_at DESC LIMIT ?',
            [$limit]
        );
    }

    /**
     * Check if a booking exists
     */
    public function exists(int $id): bool
    {
        return $this->count('SELECT COUNT(*) FROM booking WHERE bookingId = ?', [$id]) > 0;
    }

    /**
     * Check if a booking reference exists
     */
    public function refExists(string $bookingRef): bool
    {
        return $this->count('SELECT COUNT(*) FROM booking WHERE bookingRef = ?', [$bookingRef]) > 0;
    }

    /**
     * Get bookings statistics
     */
    public function getStatistics(): array
    {
        return $this->selectOne(
            'SELECT 
                COUNT(*) as totalBookings,
                SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pendingCount,
                SUM(CASE WHEN status = "confirmed" THEN 1 ELSE 0 END) as confirmedCount,
                SUM(CASE WHEN status = "cancelled" THEN 1 ELSE 0 END) as cancelledCount,
                SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completedCount,
                SUM(CASE WHEN status IN ("confirmed", "completed") THEN totalPrice ELSE 0 END) as totalRevenue
             FROM booking'
        );
    }
}