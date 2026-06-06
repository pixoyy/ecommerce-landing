<?php

namespace App\Services\Api;

use App\Services\ApiClient;

class CartApiService
{
    public function __construct(private ApiClient $client) {}

    public function getCart(): array
    {
        return $this->client->get('/cart');
    }

    public function addItem(int $variantId, int $quantity): array
    {
        return $this->client->post('/cart/items', [
            'product_variant_id' => $variantId,
            'quantity' => $quantity,
        ]);
    }

    public function updateItem(int $itemId, int $quantity): array
    {
        return $this->client->put("/cart/items/{$itemId}", [
            'quantity' => $quantity,
        ]);
    }

    public function removeItem(int $itemId): array
    {
        return $this->client->delete("/cart/items/{$itemId}");
    }
}
