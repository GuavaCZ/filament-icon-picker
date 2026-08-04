<?php

use Guava\IconPicker\Icons\Facades\IconManager;
use Guava\IconPicker\Icons\Icon;
use Guava\IconPicker\Icons\IconSet;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

beforeEach(fn () => Storage::fake('public'));

function customIconIds(): Collection
{
    return IconManager::getIcons(IconSet::CUSTOM_ID)->map(fn (Icon $icon) => $icon->id);
}

it('caches custom icon listings until invalidated', function () {
    putCustomIcon('unscoped', 'first');

    expect(customIconIds())->toContain('_gfic_icons-unscoped.first');

    putCustomIcon('unscoped', 'second');

    // Still the cached listing - the second icon was added behind the cache's back.
    expect(customIconIds())->not->toContain('_gfic_icons-unscoped.second');

    IconManager::forgetCustomIcons();

    expect(customIconIds())->toContain('_gfic_icons-unscoped.second');
});

it('lists fresh on every call when caching is disabled', function () {
    config()->set('filament-icon-picker.cache.enabled', false);

    putCustomIcon('unscoped', 'first');

    expect(customIconIds())->toContain('_gfic_icons-unscoped.first');

    putCustomIcon('unscoped', 'second');

    expect(customIconIds())->toContain('_gfic_icons-unscoped.second');
});
