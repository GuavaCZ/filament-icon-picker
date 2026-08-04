<?php

use Guava\IconPicker\Http\Controllers\IconIndexController;
use Guava\IconPicker\Http\Controllers\IconSvgController;
use Illuminate\Support\Facades\Route;

Route::middleware(config('filament-icon-picker.routes.middleware', ['web', 'throttle:120,1']))
    ->prefix(config('filament-icon-picker.routes.prefix', '_icon-picker'))
    ->as('guava-icon-picker.')
    ->group(function (): void {
        Route::get('index', IconIndexController::class)->name('index');
        Route::get('svgs', IconSvgController::class)->name('svgs');
    })
;
