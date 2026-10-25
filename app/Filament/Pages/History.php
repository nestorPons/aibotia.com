<?php 

namespace App\Filament\Pages;

use Filament\Pages\Page;

class History extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left';
    protected static ?string $title = 'Historia';
    protected static ?string $slug = 'historia';

    // Define la vista que se usará para esta página
    protected static string $view = 'filament.pages.history-page';
    public static function canAccess(): bool
    {
        return auth()->user()?->admin === 0;
    }
}
