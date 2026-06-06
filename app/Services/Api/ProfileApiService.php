<?php

namespace App\Services\Api;

use App\Services\ApiClient;

class ProfileApiService
{
    public function __construct(private ApiClient $client) {}

    public function getProfile(): array
    {
        return $this->client->get('/profile');
    }

    public function updateProfile(array $data): array
    {
        return $this->client->put('/profile', $data);
    }

    public function changePassword(array $data): array
    {
        return $this->client->put('/profile/password', $data);
    }
}
