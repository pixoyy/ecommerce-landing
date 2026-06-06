<?php

namespace App\Services\Api;

use App\Services\ApiClient;

class RewardApiService
{
    public function __construct(private ApiClient $client) {}

    public function getBalance(): array
    {
        return $this->client->get('/rewards/balance');
    }

    public function getTransactions(array $params = []): array
    {
        return $this->client->get('/rewards/transactions', $params);
    }
}
