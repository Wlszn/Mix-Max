<?php

namespace App\Domain\Models;

use App\Helpers\Core\PDOService;

class EventModel extends BaseModel
{
    /**
     * Find an event by its ID
     */
    public function findById(int $id): array|false
    {
        return $this->selectOne(
            'SELECT * FROM event WHERE eventId = ?',
            [$id]
        );
    }

    /**
     * Get all events
     */
    public function findAll(): array
    {
        return $this->selectAll('SELECT * FROM event ORDER BY date DESC, startTime DESC');
    }

    /**
     * Find events within a date range
     */
    public function findByDateRange(string $startDate, string $endDate): array
    {
        return $this->selectAll(
            'SELECT * FROM event WHERE date BETWEEN ? AND ? ORDER BY date ASC, startTime ASC',
            [$startDate, $endDate]
        );
    }

    /**
     * Find events by venue
     */
    public function findByVenue(int $venueId): array
    {
        return $this->selectAll(
            'SELECT * FROM event WHERE venueId = ? ORDER BY date DESC, startTime DESC',
            [$venueId]
        );
    }

    /**
     * Find events by artist name (partial match)
     */
    public function findByArtist(string $artist): array
    {
        return $this->selectAll(
            'SELECT * FROM event WHERE artist LIKE ? ORDER BY date DESC, startTime DESC',
            ["%{$artist}%"]
        );
    }

    /**
     * Find events by status
     */
    public function findByStatus(string $status): array
    {
        return $this->selectAll(
            'SELECT * FROM event WHERE status = ? ORDER BY date DESC, startTime DESC',
            [$status]
        );
    }

    /**
     * Find upcoming events (future dates with scheduled status)
     */
    public function findUpcoming(): array
    {
        return $this->selectAll(
            'SELECT * FROM event WHERE date >= CURDATE() AND status = "scheduled" ORDER BY date ASC, startTime ASC'
        );
    }

    /**
     * Find events by title (partial match)
     */
    public function findByTitle(string $title): array
    {
        return $this->selectAll(
            'SELECT * FROM event WHERE title LIKE ? ORDER BY date DESC, startTime DESC',
            ["%{$title}%"]
        );
    }

    /**
     * Search events by keyword in title, artist, or description
     */
    public function search(string $keyword): array
    {
        return $this->selectAll(
            'SELECT * FROM event WHERE title LIKE ? OR artist LIKE ? OR description LIKE ? ORDER BY date DESC, startTime DESC',
            ["%{$keyword}%", "%{$keyword}%", "%{$keyword}%"]
        );
    }

    /**
     * Create a new event
     * 
     * @param array $data Event data including: title, artist, description, venueId, date, startTime, endTime, imageUrl
     * @return int The new event ID
     */
    public function create(array $data): int
    {
        $this->execute(
            'INSERT INTO event (title, artist, description, venueId, date, startTime, endTime,  imageUrl) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)',
            [
                $data['title'],
                $data['artist'],
                $data['description'] ?? null,
                $data['venueId'],
                $data['date'],
                $data['startTime'],
                $data['endTime'],
                $data['imageUrl'] ?? null
            ]
        );
        
        return (int) $this->lastInsertId();
    }

    /**
     * Update an event
     */
    public function update(int $id, array $data): bool
    {
        return $this->execute(
            'UPDATE event SET 
                title = ?, 
                artist = ?, 
                description = ?, 
                venueId = ?, 
                date = ?, 
                startTime = ?, 
                endTime = ?, 
                imageUrl = ? 
             WHERE eventId = ?',
            [
                $data['title'],
                $data['artist'],
                $data['description'] ?? null,
                $data['venueId'],
                $data['date'],
                $data['startTime'],
                $data['endTime'],
                $data['imagePath'],
                $data['imageUrl'] ?? null,
                $id
            ]
        ) > 0;
    }

    /**
     * Delete an event
     */
    public function delete(int $id): bool
    {
        return $this->execute('DELETE FROM event WHERE eventId = ?', [$id]) > 0;
    }

    /**
     * Update event status
     */
    public function updateStatus(int $id, string $status): bool
    {
        return $this->execute(
            'UPDATE event SET status = ? WHERE eventId = ?',
            [$status, $id]
        ) > 0;
    }

    /**
     * Get event with venue details
     */
    public function findWithVenue(int $id): array|false
    {
        return $this->selectOne(
            'SELECT e.*, v.name as venueName, v.address, v.city, v.capacity, v.imageUrl as venueImageUrl 
             FROM event e 
             JOIN venue v ON e.venueId = v.venueId 
             WHERE e.eventId = ?',
            [$id]
        );
    }

    /**
     * Get upcoming events with venue details
     */
    public function findUpcomingWithVenue(int $limit = 10): array
    {
        return $this->selectAll(
            'SELECT e.*, v.name as venueName, v.address, v.city, v.capacity, v.imageUrl as venueImageUrl 
             FROM event e 
             JOIN venue v ON e.venueId = v.venueId 
             WHERE e.date >= CURDATE() AND e.status = "scheduled" 
             ORDER BY e.date ASC, e.startTime ASC 
             LIMIT ?',
            [$limit]
        );
    }

    /**
     * Count total events
     */
    public function countAll(): int
    {
        return $this->count('SELECT COUNT(*) FROM event');
    }

    /**
     * Count events by status
     */
    public function countByStatus(string $status): int
    {
        return $this->count('SELECT COUNT(*) FROM event WHERE status = ?', [$status]);
    }

    /**
     * Check if an event exists
     */
    public function exists(int $id): bool
    {
        return $this->count('SELECT COUNT(*) FROM event WHERE eventId = ?', [$id]) > 0;
    }
}