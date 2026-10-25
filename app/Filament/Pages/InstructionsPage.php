<?php 

namespace App\Filament\Pages;

use Filament\Pages\Page;

class InstructionsPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static ?string $title = 'Instrucciones';
    protected static ?string $slug = 'instrucciones';

    // Define la vista que se usará para esta página
    protected static string $view = 'filament.pages.list-instructions-page';
    public static function canAccess(): bool
    {
        return auth()->user()?->admin === 0;
    }
}
