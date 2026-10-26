<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ApiService
{
    protected string $uri;

    public function __construct(string $botname, string $endpoint = '')
    {
        $baseUrl = rtrim((string) config('services.api.base_url'), '/');

        if ($baseUrl === '') {
            $this->uri = '';
            return;
        }

        $path = trim("{$botname}/{$endpoint}", '/');
        $this->uri = $baseUrl . '/' . $path;
    }

    private function apiConnect()
    {
        return Http::timeout(240)
            ->withHeaders([
                'x-api-key' => config('services.api.token'),
            ]);
    }

    public function fetch(array $params = [])
    {
        if ($this->uri === '') {
            return null;
        }

        try {
            $response = $this->apiConnect()->get($this->uri, $params);
            return $response->json();
        } catch (\Throwable $e) {
            \Log::warning('API request failed', ['uri' => $this->uri, 'error' => $e->getMessage()]);
            return null;
        }
    }

    public function add($id, array $data)
    {
        if ($this->uri === '') {
            return null;
        }

        try {
            return $this->apiConnect()->post($this->uri . "/$id", $data);
        } catch (\Throwable $e) {
            \Log::warning('API request failed', ['uri' => $this->uri, 'error' => $e->getMessage()]);
            return null;
        }
    }

    public function update($id, array $data)
    {
        if ($this->uri === '') {
            return null;
        }

        try {
            return $this->apiConnect()->put($this->uri . "/$id", $data);
        } catch (\Throwable $e) {
            \Log::warning('API request failed', ['uri' => $this->uri, 'error' => $e->getMessage()]);
            return null;
        }
    }

    public function delete($index = null)
    {
        if ($this->uri === '') {
            return false;
        }

        $uri = trim($this->uri, '/');
        $endpoint = $index !== null ? $uri . "/$index" : $uri;

        try {
            return $this->apiConnect()->delete($endpoint)->successful();
        } catch (\Throwable $e) {
            \Log::warning('API request failed', ['uri' => $endpoint, 'error' => $e->getMessage()]);
            return false;
        }
    }

    public function forceReply($userId)
    {
        if ($this->uri === '') {
            return null;
        }

        $url = rtrim($this->uri, '/') . "/force-reply/$userId";

        try {
            return $this->apiConnect()->post($url, []);
        } catch (\Throwable $e) {
            \Log::warning('API request failed', ['uri' => $url, 'error' => $e->getMessage()]);
            return null;
        }
    }

    public function importUsers()
    {
        if ($this->uri === '') {
            return null;
        }

        $url = rtrim($this->uri, '/') . "/import-users";

        try {
            return $this->apiConnect()->post($url, []);
        } catch (\Throwable $e) {
            \Log::warning('API request failed', ['uri' => $url, 'error' => $e->getMessage()]);
            return null;
        }
    }
}
