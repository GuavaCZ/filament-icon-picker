---
title: Appearance
---

# Appearance

## Size

By default the icon is rendered in filament's large size. You can change it with the `size` option:

```php
use Filament\Support\Enums\IconSize;

IconColumn::make('icon')
    ->size(IconSize::Medium);
```

The available sizes are the ones from filament: `IconSize::ExtraSmall`, `IconSize::Small`, `IconSize::Medium`, `IconSize::Large`, `IconSize::ExtraLarge` and `IconSize::TwoExtraLarge`.

You can also pass `'base'` to fall back to the size the icon has on its own, without any size class:

```php
IconColumn::make('icon')
    ->size('base');
```

## Color

Icons use the current text color, so they follow your theme. If you want a specific color, use the `color` option:

```php
IconColumn::make('icon')
    ->color('primary');
```

Like everywhere in filament, this accepts a closure, which is handy to color the icon based on the record:

```php
IconColumn::make('icon')
    ->color(fn (Category $record) => $record->is_active ? 'success' : 'gray');
```

## Tooltip

```php
IconColumn::make('icon')
    ->tooltip(fn ($state) => $state);
```

## Alignment

```php
use Filament\Support\Enums\Alignment;

IconColumn::make('icon')
    ->alignment(Alignment::Center);
```
