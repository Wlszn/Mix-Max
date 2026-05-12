<?php

namespace App\Domain\Services;

use App\Domain\Models\EventModel;
use App\Domain\Models\TicketModel;
use App\Domain\Models\VenueModel;
use App\Helpers\Core\PDOService;
use Cloudinary\Cloudinary;

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

        $imageUrl = $this->resolveEventImageUrl($files);

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

    private function resolveEventImageUrl(array $files): ?string
    {
        if (
            isset($files['eventImageFile']) &&
            ($files['eventImageFile']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK
        ) {
            return $this->uploadToCloudinary($files['eventImageFile']['tmp_name']);
        }

        return null;
    }
    private function uploadToCloudinary(string $source): ?string
    {
        $cloudName = getenv('CLOUDINARY_CLOUD_NAME') ?: ($_ENV['CLOUDINARY_CLOUD_NAME'] ?? '');
        $apiKey = getenv('CLOUDINARY_API_KEY') ?: ($_ENV['CLOUDINARY_API_KEY'] ?? '');
        $apiSecret = getenv('CLOUDINARY_API_SECRET') ?: ($_ENV['CLOUDINARY_API_SECRET'] ?? '');

        if ($cloudName === '' || $apiKey === '' || $apiSecret === '') {
            return null;
        }

        $cloudinary = new Cloudinary([
            'cloud' => [
                'cloud_name' => $cloudName,
                'api_key' => $apiKey,
                'api_secret' => $apiSecret,
            ],
            'url' => [
                'secure' => true
            ]
        ]);

        $upload = $cloudinary->uploadApi()->upload($source, [
            'folder' => 'mix-max/events',
            'resource_type' => 'image'
        ]);

        return $upload['secure_url'] ?? null;
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