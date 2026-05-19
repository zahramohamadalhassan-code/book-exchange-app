<?php

namespace App\Providers;

use BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch;
use Filament\Support\Facades\FilamentView;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\HtmlString;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
            $switch
                ->locales(['ar', 'en'])
                ->visible(insidePanels: true, outsidePanels: true)
                ->circular(false)
                ->outsidePanelRoutes([
                    'auth.login',
                    'auth.profile',
                    'auth.register',
                ]);
        });

        FilamentView::registerRenderHook(
            'panels::head.end',
            fn (): HtmlString => new HtmlString(view('components.filament-rtl')->render()),
        );
    }
}
