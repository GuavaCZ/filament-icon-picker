---
title: Usage
---

# Usage

## In a resource

```php
use Guava\FilamentIconPicker\Forms\IconPicker;

public static function form(Form $form): Form
{
    return $form->schema([
        IconPicker::make('icon'),
    ]);
}
```

## In a livewire component

```php
use Filament\Forms\Form;
use Guava\FilamentIconPicker\Forms\IconPicker;

public function form(Form $form): Form
{
    return $form
        ->schema([
            IconPicker::make('icon'),
        ])
        ->statePath('data')
    ;
}
```

## In tables

```php
// Make sure this is the correct import, not the filament one
use Guava\FilamentIconPicker\Tables\IconColumn;

public static function table(Table $table): Table
{
    return $table
        ->columns([
            IconColumn::make('icon'),
        ])
        // ...
    ;
}
```

## On the frontend

The state of the field is the identifier of the selected icon, so there is nothing package specific about rendering it.

Assuming you saved the icon on your `$category` model under `icon`:

```blade
<x-icon name="{{ $category->icon }}" />
```

More information on rendering icons is in the [blade-icons documentation](https://github.com/blade-ui-kit/blade-icons#default-component).
