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

    public function findFiltered(string $search, string $category, string $date, string $sort): array
    {
        $sql = 'SELECT 
                e.*, 
                v.name as venueName, 
                v.city,
                MIN(t.price) as startingPrice
             FROM event e
             JOIN venue v ON e.venueId = v.venueId
             LEFT JOIN ticket t ON e.eventId = t.eventId';

        $where = [];
        $params = [];

        if ($search !== '') {
            $where[] = '(e.title LIKE ? 
                OR e.artist LIKE ? 
                OR e.description LIKE ? 
                OR v.name LIKE ? 
                OR v.city LIKE ?)';

            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
        }

        if ($category !== '') {
            $categoryKeywords = $this->getCategoryKeywords($category);

            if (!empty($categoryKeywords)) {
                $categoryWhere = [];

                foreach ($categoryKeywords as $keyword) {
                    $categoryWhere[] = '(e.title LIKE ? OR e.artist LIKE ? OR e.description LIKE ?)';
                    $params[] = "%{$keyword}%";
                    $params[] = "%{$keyword}%";
                    $params[] = "%{$keyword}%";
                }

                $where[] = '(' . implode(' OR ', $categoryWhere) . ')';
            }
        }

        if ($date !== '') {
            $where[] = 'e.date = ?';
            $params[] = $date;
        }

        if (!empty($where)) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }

        $sql .= ' GROUP BY e.eventId ';
        $sql .= $this->getOrderBy($sort);

        return $this->selectAll($sql, $params);
    }

    private function getCategoryKeywords(string $category): array
    {
        return match ($category) {
            'concert' => ['concert', 'music', 'singer', 'band', 'artist', 'festival', 'tour'],
            'sports' => ['sport', 'sports', 'game', 'basketball', 'soccer', 'football', 'hockey', 'nba', 'lakers', 'warriors'],
            'theater' => ['theater', 'theatre', 'play', 'musical', 'stage', 'drama', 'hamilton'],
            'comedy' => ['comedy', 'comedian', 'stand up', 'stand-up', 'laugh', 'chappelle'],
            default => [],
        };
    }

    private function getOrderBy(string $sort): string
    {
        return match ($sort) {
            'date_soonest' => 'ORDER BY e.date ASC, e.startTime ASC',
            'price_low' => 'ORDER BY startingPrice ASC, e.date ASC',
            'price_high' => 'ORDER BY startingPrice DESC, e.date ASC',
            default => 'ORDER BY e.date ASC, e.startTime ASC',
        };
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

    public function createByUser(array $data, int $userId): int
    {
        $this->execute(
            'INSERT INTO event 
            (title, artist, description, venueId, createdByUserId, date, startTime, endTime, imageUrl, status)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
            [
                $data['title'],
                $data['artist'],
                $data['description'] ?? null,
                $data['venueId'],
                $userId,
                $data['date'],
                $data['startTime'],
                $data['endTime'],
                $data['imageUrl'] ?? null,
                'pending'
            ]
        );

        return (int) $this->lastInsertId();
    }

    public function liveSearch(string $keyword): array
    {
        return $this->selectAll(
            'SELECT e.eventId, e.title, e.artist, e.date, e.startTime, e.imageUrl,
                v.name AS venueName, v.city
         FROM event e
         JOIN venue v ON e.venueId = v.venueId
         WHERE e.title LIKE ?
            OR e.artist LIKE ?
            OR e.description LIKE ?
            OR v.name LIKE ?
            OR v.city LIKE ?
         ORDER BY e.date ASC, e.startTime ASC
         LIMIT 5',
            [
                "%{$keyword}%",
                "%{$keyword}%",
                "%{$keyword}%",
                "%{$keyword}%",
                "%{$keyword}%"
            ]
        );
    }
}