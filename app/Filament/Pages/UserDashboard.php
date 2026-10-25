<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class UserDashboard extends Page
{
    protected static ?string $navigationLabel = 'Conectar dispositivo';
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';


    protected static ?string $title = '';

    protected static ?string $slug = 'inicio';
    protected static string $view = 'filament.pages.user-dashboard';
    protected static ?int $navigationSort =  -3;
    public static function canAccess(): bool
    {
        return auth()->user()?->admin === 0;
    }
}
