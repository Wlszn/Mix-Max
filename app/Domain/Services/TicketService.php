<?php

namespace App\Domain\Services;

use App\Domain\Models\TicketModel;
use App\Helpers\Core\PDOService;

class TicketService extends BaseService
{
    private TicketModel $ticketModel;

    public function __construct(PDOService $db_service)
    {
        // Initialize any dependencies or services here
        $this->ticketModel = new TicketModel($db_service);
    }

    public function createTicket(int $eventId, int $userId, string $seatNumber): bool
    {
        return $this->ticketModel->create([$eventId, $userId, $seatNumber]);
    }

    public function getTicketsByEvent(int $eventId): array
    {
        return $this->ticketModel->findByEvent($eventId);
    }

    public function getTicketById(int $ticketId): array|false
    {
        return $this->ticketModel->findById($ticketId);
    }

    public function isTicketAvailable(int $ticketId): bool
    {
        $ticket = $this->ticketModel->findById($ticketId);

        if (!$ticket) {
            return false;
        }

        return empty($ticket['heldUntil']) || strtotime($ticket['heldUntil']) < time();
    }



    public function updateTicket(int $ticketId, array $data)
    {
        // Logic to update an existing ticket
        // This could involve checking if t he ticket exists, validating the data, etc.
    }

    public function deleteTicket(int $ticketId)
    {
        // Logic to delete a ticket
        // This could involve checking if the ticket exists and then deleting it from the database
    }

}