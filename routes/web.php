<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
})->group(function () {
    Route::get('/message/edit', function () {
        return view('dashboard');
    })->name('dashboard');
});

Route::get('/admin-dashboard', fn () => view('admin.dashboard'));
Route::get('/dashboard', fn () => view('user.dashboard'));

use App\Filament\Pages\UserConversation;

Route::get('/admin/user/conversation/{id}', UserConversation::class)
    ->name('users.conversation');
