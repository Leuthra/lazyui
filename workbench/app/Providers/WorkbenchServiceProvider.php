<?php

namespace Workbench\App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Workbench\App\Models\User;

class WorkbenchServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        config([
            'auth.providers.users.model' => User::class,
            // The playground has no Vite build: its CSS comes from npm run build:workbench.
            'lazyui.vite' => [],
        ]);
    }

    public function boot(): void
    {
        Route::get('/workbench.css', fn () => response()->file(dirname(__DIR__, 2).'/public/app.css', ['Content-Type' => 'text/css']));

        Route::middleware('web')->group(function (): void {
            Route::view('/', 'lazy::demo');
        });

        View::composer('lazy::demo', function (): void {
            app('view')->startPush('head', '<link rel="stylesheet" href="'.url('/workbench.css').'">');
        });
    }
}
