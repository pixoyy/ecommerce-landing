<?php

namespace App\Services\Api;

use App\Services\ApiClient;

class ReviewApiService
{
    public function __construct(private ApiClient $client) {}

    public function submitReview(int $orderId, array $data): array
    {
        return $this->client->post("/orders/{$orderId}/reviews", $data);
    }
}
