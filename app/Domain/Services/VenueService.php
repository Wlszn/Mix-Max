<?php

declare(strict_types=1);

namespace App\Domain\Services;

use App\Domain\Models\VenueModel;
use App\Helpers\Core\PDOService;

class VenueService extends BaseService
{
    private VenueModel $venueModel;

    public function __construct (PDOService $db_service)
    {
        $this->venueModel = new VenueModel($db_service);
    }

    public function getAllVenues(): array
    {
        return $this->venueModel->findAll();
    }

    public function getVenueById(int $venueId): array|false
    {
        return $this->venueModel->findById($venueId);
    }
}