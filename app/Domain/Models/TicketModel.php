<?php

namespace App\Domain\Models;

use App\Helpers\Core\PDOService;

class TicketModel extends BaseModel
{
    /**
     * Find a ticket by its ID
     */
    public function findById(int $id): array|false
    {
        return $this->selectOne(
            'SELECT * FROM ticket WHERE ticketId = ?',
            [$id]
        );
    }

    /**
     * Get all tickets
     */
    public function findAll(): array
    {
        return $this->selectAll('SELECT * FROM ticket ORDER BY eventId, section, rowLetter, seatNumber');
    }

    /**
     * Find tickets by event
     */
    public function findByEvent(int $eventId): array
    {
        return $this->selectAll(
            'SELECT * FROM ticket WHERE eventId = ? ORDER BY section, rowLetter, seatNumber',
            [$eventId]
        );
    }

    /**
     * Find available tickets by event
     */
    public function findAvailableByEvent(int $eventId): array
    {
        return $this->selectAll(
            'SELECT * FROM ticket WHERE eventId = ? AND (heldUntil IS NULL OR heldUntil < NOW()) ORDER BY section, rowLetter, seatNumber',
            [$eventId]
        );
    }

    /**
     * Find tickets by section
     */
    public function findBySection(int $eventId, string $section): array
    {
        return $this->selectAll(
            'SELECT * FROM ticket WHERE eventId = ? AND section = ? ORDER BY rowLetter, seatNumber',
            [$eventId, $section]
        );
    }

    /**
     * Find tickets by price range
     */
    public function findByPriceRange(int $eventId, float $minPrice, float $maxPrice): array
    {
        return $this->selectAll(
            'SELECT * FROM ticket WHERE eventId = ? AND price BETWEEN ? AND ? ORDER BY price ASC',
            [$eventId, $minPrice, $maxPrice]
        );
    }

    /**
     * Get available tickets count by event
     */
    public function countAvailableByEvent(int $eventId): int
    {
        return $this->count(
            'SELECT COUNT(*) FROM ticket WHERE eventId = ? AND (heldUntil IS NULL OR heldUntil < NOW())',
            [$eventId]
        );
    }

    /**
     * Get total tickets count by event
     */
    public function countByEvent(int $eventId): int
    {
        return $this->count('SELECT COUNT(*) FROM ticket WHERE eventId = ?', [$eventId]);
    }

    /**
     * Reserve a ticket (set held status)
     */
    public function reserve(int $ticketId, string $heldUntil): bool
    {
        return $this->execute(
            'UPDATE ticket SET heldUntil = ? WHERE ticketId = ?',
            [$heldUntil, $ticketId]
        ) > 0;
    }

    /**
     * Release a ticket (clear held status)
     */
    public function release(int $ticketId): bool
    {
        return $this->execute(
            'UPDATE ticket SET heldUntil = NULL WHERE ticketId = ?',
            [$ticketId]
        ) > 0;
    }

    /**
     * Create a new ticket
     * 
     * @param array $data Ticket data including: eventId, section, rowLetter, seatNumber, price
     * @return int The new ticket ID
     */
    public function create(array $data): int
    {
        $this->execute(
            'INSERT INTO ticket (eventId, section, rowLetter, seatNumber, price) VALUES (?, ?, ?, ?, ?)',
            [
                $data['eventId'],
                $data['section'],
                $data['rowLetter'],
                $data['seatNumber'],
                $data['price']
            ]
        );
        
        return (int) $this->lastInsertId();
    }

    /**
     * Create multiple tickets at once
     * 
     * @param array $tickets Array of ticket data arrays
     * @return array Array of new ticket IDs
     */
    public function createMultiple(array $tickets): array
    {
        $ids = [];
        $this->beginTransaction();
        
        try {
            foreach ($tickets as $ticketData) {
                $this->execute(
                    'INSERT INTO ticket (eventId, section, rowLetter, seatNumber, price) VALUES (?, ?, ?, ?, ?)',
                    [
                        $ticketData['eventId'],
                        $ticketData['section'],
                        $ticketData['rowLetter'],
                        $ticketData['seatNumber'],
                        $ticketData['price']
                    ]
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
     * Update a ticket
     */
    public function update(int $id, array $data): bool
    {
        return $this->execute(
            'UPDATE ticket SET 
                section = ?, 
                rowLetter = ?, 
                seatNumber = ?, 
                price = ? 
             WHERE ticketId = ?',
            [
                $data['section'],
                $data['rowLetter'],
                $data['seatNumber'],
                $data['price'],
                $id
            ]
        ) > 0;
    }

    /**
     * Delete a ticket
     */
    public function delete(int $id): bool
    {
        return $this->execute('DELETE FROM ticket WHERE ticketId = ?', [$id]) > 0;
    }

    /**
     * Delete all tickets for an event
     */
    public function deleteByEvent(int $eventId): bool
    {
        return $this->execute('DELETE FROM ticket WHERE eventId = ?', [$eventId]) > 0;
    }

    /**
     * Get ticket with event details
     */
    public function findWithEvent(int $id): array|false
    {
        return $this->selectOne(
            'SELECT t.*, e.title, e.artist, e.date, e.startTime, e.endTime, e.status as eventStatus,
                    v.name as venueName, v.address, v.city
             FROM ticket t
             JOIN event e ON t.eventId = e.eventId
             JOIN venue v ON e.venueId = v.venueId
             WHERE t.ticketId = ?',
            [$id]
        );
    }

    /**
     * Get expired holds (tickets that were held but reservation expired)
     */
    public function findExpiredHolds(): array
    {
        return $this->selectAll(
            'SELECT * FROM ticket WHERE heldUntil IS NOT NULL AND heldUntil < NOW()'
        );
    }

    /**
     * Clear expired holds
     */
    public function clearExpiredHolds(): int
    {
        return $this->execute('UPDATE ticket SET heldUntil = NULL WHERE heldUntil IS NOT NULL AND heldUntil < NOW()');
    }

    /**
     * Check if a ticket exists
     */
    public function exists(int $id): bool
    {
        return $this->count('SELECT COUNT(*) FROM ticket WHERE ticketId = ?', [$id]) > 0;
    }

    /**
     * Check if a specific seat exists for an event
     */
    public function seatExists(int $eventId, string $section, string $rowLetter, string $seatNumber): bool
    {
        return $this->count(
            'SELECT COUNT(*) FROM ticket WHERE eventId = ? AND section = ? AND rowLetter = ? AND seatNumber = ?',
            [$eventId, $section, $rowLetter, $seatNumber]
        ) > 0;
    }

    /**
     * Get price statistics for an event
     */
    public function getPriceStats(int $eventId): array|false
    {
        return $this->selectOne(
            'SELECT MIN(price) as minPrice, MAX(price) as maxPrice, AVG(price) as avgPrice 
             FROM ticket WHERE eventId = ?',
            [$eventId]
        );
    }
}