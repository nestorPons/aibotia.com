<?php 

namespace App\Filament\Pages;

use Filament\Pages\Page;

class UsersPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static ?string $title = 'Usuarios';
    protected static ?string $navigationLabel = 'Usuarios';
    protected static ?string $slug = '';

    // Define la vista que se usará para esta página
    protected static string $view = 'filament.pages.list-users-page';
    protected static ?int $navigationSort = -3;
    public static function canAccess(): bool
    {
        return auth()->user()?->admin === 0;
    }
    
}
