<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Http;

class UsersTable extends Component
{
    public $data = [];

    public function mount()
    {
        $this->fetchMessages();
    }

    public function fetchMessages()
    {
        // Obtener los datos de la API
        $response = Http::withHeaders([
            'x-api-key' => config('services.api.token'),
        ])->get(config('services.api.base_url') . '/users/');


        $this->data = collect($response->json())
            ->map(function ($data) {
                foreach ($data as $key => $value) {
                    if (is_string($value)) {
                        $decoded = json_decode($value, true);
                        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                            $data[$key] = $decoded;
                        }
                    }
                }
                return $data;
            })
            ->values()
            ->all();
            
    }
    

    public function render()
    {
        return view('livewire.table-users');
    }
}
