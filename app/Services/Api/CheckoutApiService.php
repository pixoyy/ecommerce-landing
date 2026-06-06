<?php

namespace App\Services\Api;

use App\Services\ApiClient;

class CheckoutApiService
{
    public function __construct(private ApiClient $client) {}

    public function checkout(array $data): array
    {
        return $this->client->post('/checkout', $data);
    }
}
