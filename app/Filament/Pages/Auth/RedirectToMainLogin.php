<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Auth\Login as BaseLogin;

class RedirectToMainLogin extends BaseLogin
{
    public function mount(): void
    {
        if (auth()->check()) {
            $this->redirectIntended(default: '/admin');
            return;
        }

        $this->redirect(route('login'));
    }
}
