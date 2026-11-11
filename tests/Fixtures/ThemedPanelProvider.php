<?php

namespace LazyUI\Tests\Fixtures;

use Filament\Panel;
use Filament\PanelProvider;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use LazyUI\Filament\LazyUIPlugin;

/** A panel whose own Vite theme imports LazyUI's source CSS. */
class ThemedPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('themed')
            ->path('themed')
            ->login()
            ->plugin(LazyUIPlugin::make()->precompiled(false))
            ->middleware([EncryptCookies::class, StartSession::class, ShareErrorsFromSession::class]);
    }
}
