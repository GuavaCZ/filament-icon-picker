---
title: Basic usage
---

# Basic usage

To display a stored icon in your filament tables, use our `IconColumn` class:

```php
// Make sure this is the correct import, not the filament one
use Guava\IconPicker\Tables\Columns\IconColumn;

$table
    ->columns([
        IconColumn::make('icon'),
    ])
    // ...
;
```

> [!IMPORTANT]
> Filament has an `IconColumn` of its own, and both are called the same. If your icons don't render, please check that you imported `Guava\IconPicker\Tables\Columns\IconColumn`.

The reason for our own column is that filament's version expects you to map the state to an icon yourself. Our column takes the icon name straight from the state, which is exactly what the field stored.

## Unknown icons

If the stored value doesn't resolve to a real icon, for example because you uninstalled the kit it came from, the column renders its placeholder instead of failing.

```php
IconColumn::make('icon')
    ->placeholder('No icon');
```

You can also use an icon as the placeholder:

```php
use Filament\Support\Icons\Heroicon;

IconColumn::make('icon')
    ->placeholder(Heroicon::OutlinedQuestionMarkCircle);
```
