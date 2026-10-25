<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class AssistantPrompt extends Page
{
    protected static bool $shouldRegisterNavigation = true;
    protected static ?string $navigationIcon = 'heroicon-o-cpu-chip';
    protected static string $view = 'filament.pages.assistant-prompt';
    protected static ?string $title = 'Prompt';
    protected static ?string $slug = 'assistant-prompt';

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }
}
