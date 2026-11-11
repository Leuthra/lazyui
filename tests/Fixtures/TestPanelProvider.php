<?php

namespace LazyUI\Tests\Fixtures;

use Filament\Http\Middleware\Authenticate;
use Filament\Panel;
use Filament\PanelProvider;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use LazyUI\Filament\LazyUIPlugin;

class TestPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->plugin(LazyUIPlugin::make())
            ->middleware([EncryptCookies::class, StartSession::class, ShareErrorsFromSession::class])
            ->authMiddleware([Authenticate::class]);
    }
}
