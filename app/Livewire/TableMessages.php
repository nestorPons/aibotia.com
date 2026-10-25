<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Http;

class TableMessages extends Component
{
    public $messages = [];

    public function mount()
    {
        $this->fetchMessages();
    }

    public function fetchMessages()
    {
        // Obtener los datos de la API
        $response = Http::withHeaders([
            'x-api-key' => config('services.api.token'),
        ])->get(config('services.api.base_url') . '/bot-messages');


        $this->messages = collect($response->json())
            ->map(function ($message) {
                foreach ($message as $key => $value) {
                    if (is_string($value)) {
                        $decoded = json_decode($value, true);
                        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                            $message[$key] = $decoded;
                        }
                    }
                }
                unset($message['created_at'], $message['updated_at']);
                return $message;
            })
            ->values()
            ->all();
            
    }
    

    public function render()
    {
        return view('livewire.table-messages');
    }
}
