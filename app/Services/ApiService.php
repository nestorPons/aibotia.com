<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ApiService
{
    protected string $uri;

    public function __construct(string $botname, string $endpoint = '')
    {
        $this->uri = config('services.api.base_url') . "/{$botname}/{$endpoint}";
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
        $response = $this->apiConnect()->get($this->uri, $params);
        return $response->json();
    }

    public function add($id, array $data)
    {
        return $this->apiConnect()->add($this->uri . "/$id", $data);
    }

    public function update($id, array $data)
    {
        return $this->apiConnect()->put($this->uri . "/$id", $data);
    }

    public function delete($index = null)
    {
        $uri = trim($this->uri, '/');
        $endpoint = $index !== null ? $uri . "/$index" : $uri;
        
        $return = $this->apiConnect()->delete($endpoint);
        return $return->successful();
    }

    public function forceReply($userId)
    {
        // Esto asume que el endpoint base es .../users (de tu __construct)
        $url = rtrim($this->uri, '/') . "/force-reply/$userId";
        return $this->apiConnect()->post($url, []);
    }

    public function importUsers()
    {
        // Esto asume que el endpoint base es .../users (de tu __construct)
        $url = rtrim($this->uri, '/') . "/import-users";
        return $this->apiConnect()->post($url, []);
    }
}
