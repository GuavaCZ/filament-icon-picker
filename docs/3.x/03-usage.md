---
title: Usage
---

# Usage

## In schemas

Add the icon picker to any form schema in your filament panel, or to any livewire component that supports filament forms:

```php
use Guava\IconPicker\Forms\Components\IconPicker;

IconPicker::make('icon');
```

The field stores the full icon name as a string, for example `heroicon-o-academic-cap`, so the column in your database is a regular string column:

```php
$table->string('icon')->nullable();
```

## In tables

To display the stored icon in your filament tables, use our `IconColumn` class:

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
> Filament has an `IconColumn` of its own and both are called the same. If your icons don't render, please check that you imported `Guava\IconPicker\Tables\Columns\IconColumn`.

## On the frontend

We store the full icon name in your database, so there is nothing package specific about rendering it. Treat it as you would any other static icon.

Assuming you saved the icon on your `$category` model under `icon`:

```blade
<x-icon :name="$category->icon" />
```

More information on rendering icons is in the [blade-icons documentation](https://github.com/blade-ui-kit/blade-icons#default-component).
