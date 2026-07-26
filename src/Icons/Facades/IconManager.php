<?php

namespace Guava\IconPicker\Icons\Facades;

use Guava\IconPicker\Forms\Components\IconPicker;
use Illuminate\Support\Facades\Facade;

/**
 * @see IconPicker
 */
class IconManager extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Guava\IconPicker\Icons\IconManager::class;
    }
}
