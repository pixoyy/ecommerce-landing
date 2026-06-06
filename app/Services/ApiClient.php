<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ApiClient
{
    private string $baseUrl;

    private int $timeout;

    public function __construct()
    {
        $this->baseUrl = config('api.base_url');
        $this->timeout = config('api.timeout');
    }

    public function get(string $endpoint, array $params = []): array
    {
        $response = Http::timeout($this->timeout)
            ->get("{$this->baseUrl}{$endpoint}", $params);

        return $response->json();
    }

    public function post(string $endpoint, array $data = []): array
    {
        $response = Http::timeout($this->timeout)
            ->post("{$this->baseUrl}{$endpoint}", $data);

        return $response->json();
    }

    public function put(string $endpoint, array $data = []): array
    {
        $response = Http::timeout($this->timeout)
            ->put("{$this->baseUrl}{$endpoint}", $data);

        return $response->json();
    }

    public function delete(string $endpoint): array
    {
        $response = Http::timeout($this->timeout)
            ->delete("{$this->baseUrl}{$endpoint}");

        return $response->json();
    }
}
