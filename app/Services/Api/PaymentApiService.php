<?php

namespace App\Services\Api;

use App\Services\ApiClient;

class PaymentApiService
{
    public function __construct(private ApiClient $client) {}

    public function getPaymentAccounts(): array
    {
        return $this->client->get('/payment-accounts');
    }

    public function uploadPayment(int $orderId, array $data): array
    {
        return $this->client->post("/orders/{$orderId}/payment", $data);
    }
}
