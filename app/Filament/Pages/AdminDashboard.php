<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class AdminDashboard extends Page 
{

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static string $view = 'filament.pages.admin-dashboard';
    protected static ?string $navigationGroup = 'Admin';
    protected static ?string $title = 'Admin';
    protected static ?string $slug = 'admin-dashboard';

    public static function canAccess(): bool
    {
        return auth()->user()?->admin === 1;
    }

}
