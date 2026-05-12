<?php

namespace App\Domain\Services;

use App\Domain\Models\EventModel;
use App\Domain\Models\TicketModel;
use App\Domain\Models\VenueModel;
use App\Helpers\Core\PDOService;

class EventService extends BaseService
{
    private EventModel $eventModel;
    private TicketModel $ticketModel;
    private VenueModel $venueModel;
    public function __construct(PDOService $db_service)
    {
        $this->eventModel = new EventModel($db_service);
        $this->ticketModel = new TicketModel($db_service);
        $this->venueModel = new VenueModel($db_service);
    }

    public function createEvent(array $data): int
    {
        return $this->eventModel->create($data);
    }

    public function createUserEvent(array $data, array $files, int $userId): bool
    {
        $venueId = $this->venueModel->create([
            'name' => $data['venueName'],
            'address' => $data['address'],
            'city' => $data['city'],
            'capacity' => (int) $data['capacity'],
            'imageUrl' => null
        ]);

        $imageUrl = !empty($data['imageUrl']) ? trim($data['imageUrl']) : null;

        $imageUrl = null;


        if (
            isset($files['eventImageFile']) &&
            $files['eventImageFile']['error'] === UPLOAD_ERR_OK
        ) {
            $uploadDir = APP_BASE_DIR_PATH . '/public/uploads/events/';

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $extension = pathinfo(
                $files['eventImageFile']['name'],
                PATHINFO_EXTENSION
            );

            $fileName = uniqid('event_', true) . '.' . $extension;

            move_uploaded_file(
                $files['eventImageFile']['tmp_name'],
                $uploadDir . $fileName
            );

            $imageUrl = '/uploads/events/' . $fileName;
        }


        if (!$imageUrl && !empty($data['imageUrl'])) {
            $imageUrl = trim($data['imageUrl']);
        }

        $eventId = $this->eventModel->createAndReturnId([
            'title' => $data['title'],
            'artist' => $data['artist'],
            'category' => $data['category'],
            'description' => $data['description'],
            'venueId' => $venueId,
            'createdByUserId' => $userId,
            'date' => $data['date'],
            'startTime' => $data['startTime'],
            'endTime' => $data['endTime'],
            'imageUrl' => $imageUrl,
            'status' => 'pending'
        ]);

        $this->ticketModel->generateTicketsForEvent(
            $eventId,
            $data['ticketSection'],
            (float) $data['ticketPrice'],
            (int) $data['ticketQuantity']
        );

        return true;
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

    public function getFeaturedEvents(int $limit = 3): array
    {
        return array_slice($this->getAllEvents(), 0, $limit);
    }

    public function getPendingEvents(): array
    {
        return $this->eventModel->findPendingEvents();
    }
    public function updateEventStatus(int $eventId, string $status): bool
    {
        return $this->eventModel->updateStatus($eventId, $status);
    }

    public function getAdminDashboardData(): array
    {
        return [
            'stats' => $this->eventModel->getDashboardStats(),
            'recentEvents' => $this->eventModel->getRecentEventsForDashboard(),
            'categoryData' => $this->eventModel->getCategoryStats(),
        ];
    }

    public function getAllEventsForAdmin(): array
    {
        return $this->eventModel->findAllForAdmin();
    }




}