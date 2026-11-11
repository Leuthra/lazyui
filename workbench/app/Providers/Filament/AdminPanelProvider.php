<?php

namespace Workbench\App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use LazyUI\Filament\LazyUIPlugin;

/**
 * The workbench panel: every Filament feature with LazyUI (precompiled mode, no custom theme).
 * php artisan-style entry point: vendor/bin/testbench serve  →  /admin  (admin@lazyui.test / password)
 */
class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->brandName('LazyUI Shop')
            ->login()
            ->registration()
            ->passwordReset()
            ->profile()
            ->databaseNotifications()
            ->globalSearchKeyBindings(['mod+k'])
            ->sidebarCollapsibleOnDesktop()
            // Benchmark switch: cookie lazyui_bench=off renders the same panel without LazyUI.
            ->plugins(($_COOKIE['lazyui_bench'] ?? null) === 'off' ? [] : [LazyUIPlugin::make()])
            ->discoverResources(in: dirname(__DIR__, 2).'/Filament/Resources', for: 'Workbench\\App\\Filament\\Resources')
            ->discoverPages(in: dirname(__DIR__, 2).'/Filament/Pages', for: 'Workbench\\App\\Filament\\Pages')
            ->discoverWidgets(in: dirname(__DIR__, 2).'/Filament/Widgets', for: 'Workbench\\App\\Filament\\Widgets')
            ->pages([Dashboard::class])
            ->widgets([AccountWidget::class])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([Authenticate::class]);
    }
}
