<?php

namespace App\Domain\Models;

use App\Helpers\Core\PDOService;

class VenueModel extends BaseModel
{
    /**
     * Find a venue by its ID
     */
    public function findById(int $id): array|false
    {
        return $this->selectOne(
            'SELECT * FROM venue WHERE venueId = ?',
            [$id]
        );
    }

    /**
     * Get all venues
     */
    public function findAll(): array
    {
        return $this->selectAll('SELECT * FROM venue ORDER BY name ASC');
    }

    /**
     * Find venues by city
     */
    public function findByCity(string $city): array
    {
        return $this->selectAll(
            'SELECT * FROM venue WHERE city = ? ORDER BY name ASC',
            [$city]
        );
    }

    /**
     * Find venues by city (partial match)
     */
    public function findByCityLike(string $city): array
    {
        return $this->selectAll(
            'SELECT * FROM venue WHERE city LIKE ? ORDER BY name ASC',
            ["%{$city}%"]
        );
    }

    /**
     * Find venues by name (partial match)
     */
    public function findByName(string $name): array
    {
        return $this->selectAll(
            'SELECT * FROM venue WHERE name LIKE ? ORDER BY name ASC',
            ["%{$name}%"]
        );
    }

    /**
     * Search venues by keyword in name, address, or city
     */
    public function search(string $keyword): array
    {
        return $this->selectAll(
            'SELECT * FROM venue WHERE name LIKE ? OR address LIKE ? OR city LIKE ? ORDER BY name ASC',
            ["%{$keyword}%", "%{$keyword}%", "%{$keyword}%"]
        );
    }

    /**
     * Create a new venue
     * 
     * @param array $data Venue data including: name, address, city, capacity, imageUrl
     * @return int The new venue ID
     */
    public function create(array $data): int
    {
        $this->execute(
            'INSERT INTO venue (name, address, city, capacity, imageUrl) VALUES (?, ?, ?, ?, ?)',
            [
                $data['name'],
                $data['address'],
                $data['city'],
                $data['capacity'],
                $data['imageUrl'] ?? null
            ]
        );
        
        return (int) $this->lastInsertId();
    }

    /**
     * Update a venue
     */
    public function update(int $id, array $data): bool
    {
        return $this->execute(
            'UPDATE venue SET 
                name = ?, 
                address = ?, 
                city = ?, 
                capacity = ?, 
                imageUrl = ? 
             WHERE venueId = ?',
            [
                $data['name'],
                $data['address'],
                $data['city'],
                $data['capacity'],
                $data['imageUrl'] ?? null,
                $id
            ]
        ) > 0;
    }

    /**
     * Delete a venue
     */
    public function delete(int $id): bool
    {
        return $this->execute('DELETE FROM venue WHERE venueId = ?', [$id]) > 0;
    }

    /**
     * Get venue with event count
     */
    public function findWithEventCount(int $id): array|false
    {
        return $this->selectOne(
            'SELECT v.*, COUNT(e.eventId) as eventCount 
             FROM venue v 
             LEFT JOIN event e ON v.venueId = e.venueId 
             WHERE v.venueId = ? 
             GROUP BY v.venueId',
            [$id]
        );
    }

    /**
     * Get all venues with event count
     */
    public function findAllWithEventCount(): array
    {
        return $this->selectAll(
            'SELECT v.*, COUNT(e.eventId) as eventCount 
             FROM venue v 
             LEFT JOIN event e ON v.venueId = e.venueId 
             GROUP BY v.venueId 
             ORDER BY v.name ASC'
        );
    }

    /**
     * Get distinct cities with venues
     */
    public function getCities(): array
    {
        return $this->selectAll('SELECT DISTINCT city FROM venue ORDER BY city ASC');
    }

    /**
     * Count total venues
     */
    public function countAll(): int
    {
        return $this->count('SELECT COUNT(*) FROM venue');
    }

    /**
     * Get total capacity of all venues
     */
    public function totalCapacity(): int
    {
        return (int) $this->count('SELECT SUM(capacity) FROM venue');
    }

    /**
     * Check if a venue exists
     */
    public function exists(int $id): bool
    {
        return $this->count('SELECT COUNT(*) FROM venue WHERE venueId = ?', [$id]) > 0;
    }

    /**
     * Find venues with available capacity (has upcoming events)
     */
    public function findActive(): array
    {
        return $this->selectAll(
            'SELECT DISTINCT v.* 
             FROM venue v 
             JOIN event e ON v.venueId = e.venueId 
             WHERE e.date >= CURDATE() AND e.status = "scheduled" 
             ORDER BY v.name ASC'
        );
    }
}