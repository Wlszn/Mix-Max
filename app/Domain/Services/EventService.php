<?php

namespace App\Domain\Services;

use App\Domain\Models\EventModel;
use App\Domain\Models\TicketModel;
use App\Helpers\Core\PDOService;

class EventService extends BaseService
{
    private EventModel $eventModel;
    private TicketModel $ticketModel;

    public function __construct(PDOService $db_service)
    {
        $this->eventModel = new EventModel($db_service);
        $this->ticketModel = new TicketModel($db_service);
    }

    public function createEvent(array $data): int
    {
        return $this->eventModel->create($data);
    }

    public function createUserEvent(array $data, int $userId): int
    {
        return $this->eventModel->createByUser($data, $userId);
    }

    public function getAllEvents(): array
    {
        return $this->eventModel->findAll();
    }

    public function getEventById(int $eventId): array|false
    {
        return $this->eventModel->findById($eventId);
    }

    public function searchEvents(string $keyword): array
    {
        $keyword = trim($keyword);

        if ($keyword === '') {
            return $this->getAllEvents();
        }

        return $this->eventModel->search($keyword);
    }

    public function filterEvents(string $search, string $category, string $date, string $sort): array
    {
        $search = trim($search);
        $category = trim($category);
        $date = trim($date);
        $sort = trim($sort);

        return $this->eventModel->findFiltered($search, $category, $date, $sort);
    }

    public function updateEvent(int $eventId, array $data): bool
    {
        if (!$this->eventModel->findById($eventId)) {
            return false;
        }

        return $this->eventModel->update($eventId, $data);
    }

    public function deleteEvent(int $eventId): bool
    {
        if (!$this->eventModel->findById($eventId)) {
            return false;
        }

        return $this->eventModel->delete($eventId);
    }

    public function liveSearchEvents(string $keyword): array
    {
        $keyword = trim($keyword);

        if (strlen($keyword) < 2) {
            return [];
        }

        return $this->eventModel->liveSearch($keyword);
    }

    public function getSimilarEvents(int $eventId, string $category, string $city): array
    {
        return $this->eventModel->findSimilar($eventId, $category, $city);
    }

}