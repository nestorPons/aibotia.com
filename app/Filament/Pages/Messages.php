<?php 

namespace App\Filament\Pages;

use Filament\Pages\Page;

class Messages extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left';
    protected static ?string $title = 'Mensajes';
    protected static ?string $slug = 'mensajes';

    // Define la vista que se usará para esta página
    protected static string $view = 'filament.pages.table-messages-page';
    public static function canAccess(): bool
    {
        return auth()->user()?->admin === 0;
    }
}
