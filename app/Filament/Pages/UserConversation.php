<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Services\ApiService;

class UserConversation extends Page
{
    // 🔹 No mostrar en el menú lateral
    protected static bool $shouldRegisterNavigation = false;

    // 🔹 Vista Blade que se usará
    protected static string $view = 'livewire.conversation';

    // 🔹 Título por defecto (puedes sobrescribirlo en getTitle())
    protected static ?string $title = 'Conversación';

    public ?string $userId = null;
    public ?string $userName = null;
    public array $conversation = [];

    public function mount(string $id): void
    {
        $this->userId = $id;
        $api = new ApiService(auth()->user()->botname, "users/conversation/{$id}");
        $response = $api->fetch();
        $this->userName = $response['user']['name'] ?? 'Desconocido';
        $this->conversation = $response['conversation'] ?? [];
    }

    public function getTitle(): string
    {
        // Aquí ya no es static ✅
        return "Conversación con {$this->userName} (ID: {$this->userId})";
    }

    public function formatMessage($text) 
{
    return nl2br(e($text));
}

}