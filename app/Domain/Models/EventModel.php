<?php

namespace App\Domain\Models;

class EventModel extends BaseModel
{
    public function findById(int $id): array|false
    {
        return $this->selectOne(
            'SELECT 
                e.*, 
                v.name as venueName, 
                v.address, 
                v.city, 
                v.capacity,
                MIN(t.price) as startingPrice
             FROM event e
             JOIN venue v ON e.venueId = v.venueId
             LEFT JOIN ticket t ON e.eventId = t.eventId
             WHERE e.eventId = ?
             GROUP BY e.eventId',
            [$id]
        );
    }

    public function findAll(): array
    {
        return $this->selectAll(
            'SELECT 
                e.*, 
                v.name as venueName, 
                v.city,
                MIN(t.price) as startingPrice
             FROM event e
             JOIN venue v ON e.venueId = v.venueId
             LEFT JOIN ticket t ON e.eventId = t.eventId
             GROUP BY e.eventId
             ORDER BY e.date ASC, e.startTime ASC'
        );
    }

    public function search(string $keyword): array
    {
        return $this->selectAll(
            'SELECT 
                e.*, 
                v.name as venueName, 
                v.city,
                MIN(t.price) as startingPrice
             FROM event e
             JOIN venue v ON e.venueId = v.venueId
             LEFT JOIN ticket t ON e.eventId = t.eventId
             WHERE e.title LIKE ? 
                OR e.artist LIKE ? 
                OR e.description LIKE ? 
                OR v.name LIKE ? 
                OR v.city LIKE ?
             GROUP BY e.eventId
             ORDER BY e.date ASC, e.startTime ASC',
            ["%{$keyword}%", "%{$keyword}%", "%{$keyword}%", "%{$keyword}%", "%{$keyword}%"]
        );
    }

    public function create(array $data): int
    {
        $this->execute(
            'INSERT INTO event (title, artist, description, venueId, date, startTime, endTime, imageUrl)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
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
                $data['imageUrl'] ?? null,
                $id
            ]
        );
    }

    public function delete(int $id): bool
    {
        return $this->execute('DELETE FROM event WHERE eventId = ?', [$id]);
    }
}