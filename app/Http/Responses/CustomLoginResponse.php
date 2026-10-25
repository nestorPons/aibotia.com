<?php

namespace App\Http\Responses;

use Filament\Http\Responses\Auth\Contracts\LoginResponse as LoginResponseContract;
use Illuminate\Support\Facades\Auth;

class CustomLoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        $user = Auth::user();

        // Redirigir a una página específica DENTRO del panel Filament
        if ($user->admin == 1) {
            return redirect()->intended('/admin/users');
        }

        return redirect()->intended('/admin/inicio');

    }
}
