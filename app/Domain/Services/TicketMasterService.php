<?php

declare(strict_types=1);

namespace App\Domain\Services;

class TicketmasterService extends BaseService
{
    private string $apiKey;
    private string $baseUrl = 'https://app.ticketmaster.com/discovery/v2';

    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public function getEvents(array $filters = []): array
    {
        $params = array_merge([
            'apikey' => $this->apiKey,
            'size' => 20,
            'sort' => 'date,asc',
            'countryCode' => 'CA'
        ], $filters);

        $url = $this->baseUrl . '/events.json?' . http_build_query($params);

        $response = file_get_contents($url);

        if ($response === false) {
            return [];
        }

        $data = json_decode($response, true);

        return $data['_embedded']['events'] ?? [];
    }

    public function getEventById(string $eventId): array|false
    {
        $url = $this->baseUrl . '/events/' . urlencode($eventId) . '.json?apikey=' . urlencode($this->apiKey);

        $response = file_get_contents($url);

        if ($response === false) {
            return false;
        }

        $data = json_decode($response, true);

        return is_array($data) ? $data : false;
    }
}