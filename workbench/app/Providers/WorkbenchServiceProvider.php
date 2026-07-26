<?php

namespace Workbench\App\Providers;

use Filament\Support\Assets\Theme;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class WorkbenchServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Rebuild with `npm run build:theme`. Without it the picker renders unstyled.
        FilamentAsset::register([
            Theme::make('workbench', dirname(__DIR__, 2) . '/dist/theme.css'),
        ], 'workbench');

        Route::view('/', 'welcome');
    }
}
