<?php

namespace App\Services\Api;

use App\Services\ApiClient;

class CatalogApiService
{
    public function __construct(private ApiClient $client) {}

    public function getCategories(array $params = []): array
    {
        return $this->client->get('/categories', $params);
    }

    public function getBrands(array $params = []): array
    {
        return $this->client->get('/brands', $params);
    }

    public function getProducts(array $filters = []): array
    {
        return $this->client->get('/products', $filters);
    }

    public function getProductBySlug(string $slug): array
    {
        return $this->client->get("/products/{$slug}");
    }

    public function getProductReviews(int $productId, array $params = []): array
    {
        return $this->client->get("/products/{$productId}/reviews", $params);
    }
}
