<?php

namespace App\Services\Api;

use App\Services\ApiClient;

class ShipmentApiService
{
    public function __construct(private ApiClient $client) {}

    public function getTracking(int $orderId): array
    {
        return $this->client->get("/orders/{$orderId}/tracking");
    }
}
