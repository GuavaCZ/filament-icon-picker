---
title: Upgrading
---

# Upgrading

This guide covers the upgrade from 2.x to 3.x. Version 3.x exists for filament 4.

This is the biggest step in the history of the package. The field was rewritten, so the namespace, the setup and most of the options changed.

## Upgrade filament

Please follow the [filament upgrade guide](https://filamentphp.com/docs/4.x/upgrade-guide) first.

## Bump the constraint

```bash
composer require guava/filament-icon-picker:"^3.0"
```

## Change your imports

The namespace changed from `Guava\FilamentIconPicker` to `Guava\IconPicker`, and both classes moved:

```php
// Before
use Guava\FilamentIconPicker\Forms\IconPicker;
use Guava\FilamentIconPicker\Tables\IconColumn;

// After
use Guava\IconPicker\Forms\Components\IconPicker;
use Guava\IconPicker\Tables\Columns\IconColumn;
```

## Publish the assets

The field now ships its own JS, so you need to publish the package assets:

```bash
php artisan filament:assets
```

## Add a custom theme

A custom filament theme is now **required**. If you don't have one, please read the [filament documentation](https://filamentphp.com/docs/4.x/styling/overview#creating-a-custom-theme) on how to create one.

Then add the following to your **theme.css** file:

```css
@source '../../../../vendor/guava/filament-icon-picker/resources/**/*';
```

If you skip this, the picker renders without any styling.

## Remove the config file

`config/icon-picker.php` is not used anymore and can be deleted. Everything is configured on the field itself.

## Options that are gone

The following options were removed and have no replacement:

| Removed | What to do |
|---------|------------|
| `columns()` | The result views have their own responsive grids now, see [customization](04-customization.md) |
| `layout()` | Replaced by `dropdown()`. `Layout::FLOATING` is the default, `Layout::ON_TOP` becomes `->dropdown(false)` |
| `itemTemplate()` | Replaced by `searchResultsView()`, which replaces the whole result list instead of a single item |
| `allowIcons()` | No replacement. You can still limit whole sets with `sets()` |
| `disallowIcons()` | No replacement |
| `cacheable()` | No replacement, the field no longer caches search results |
| `cacheDuration()` | No replacement |

## Options that stayed

`sets()` works exactly the same:

```php
IconPicker::make('icon')
    ->sets(['heroicons']);
```

## Found an issue?

If you find a step that is missing here, please open a PR and modify this file. We will review it and merge it.
