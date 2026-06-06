<?php

namespace App\Services\Api;

use App\Services\ApiClient;

class OrderApiService
{
    public function __construct(private ApiClient $client) {}

    public function getOrders(array $params = []): array
    {
        return $this->client->get('/orders', $params);
    }

    public function getOrderByNumber(string $orderNumber): array
    {
        return $this->client->get("/orders/{$orderNumber}");
    }

    public function cancelOrder(int $orderId): array
    {
        return $this->client->post("/orders/{$orderId}/cancel");
    }
}
