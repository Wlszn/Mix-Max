<?php

namespace App\Domain\Models;

use App\Helpers\Core\PDOService;
use App\Helpers\Core\Service;

class BookingModel extends BaseModel
{
public function findById(int $id): array|false
    {
        return $this->selectOne('SELECT * FROM booking WHERE bookingId = ?', [$id]);
    }
}