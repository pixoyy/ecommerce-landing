<?php

namespace App\Services\Api;

use App\Services\ApiClient;

class AuthApiService
{
    public function __construct(private ApiClient $client) {}

    public function register(array $data): array
    {
        return $this->client->post('/register', $data);
    }

    public function login(string $email, string $password): array
    {
        return $this->client->post('/login', [
            'email' => $email,
            'password' => $password,
        ]);
    }

    public function logout(): array
    {
        return $this->client->post('/logout');
    }

    public function me(): array
    {
        return $this->client->get('/me');
    }
}
