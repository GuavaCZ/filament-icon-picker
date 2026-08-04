---
title: Basic usage
---

# Basic usage

Add the icon picker to any form schema in your filament panel, or to any livewire component that supports filament schemas:

```php
use Guava\IconPicker\Forms\Components\IconPicker;

IconPicker::make('icon');
```

The field stores the full icon name as a string, for example `heroicon-o-academic-cap`. So the column in your database is a regular string column:

```php
$table->string('icon')->nullable();
```

Since the state is just a string, everything you know from other filament fields works as usual:

```php
IconPicker::make('icon')
    ->label('Category icon')
    ->required()
    ->helperText('Shown next to the category on your website.');
```

## Placeholder

While no icon is selected, the field shows a placeholder. You can change it:

```php
IconPicker::make('icon')
    ->placeholder('Pick an icon');
```

## Validation

The field validates its own state for you. When the form is submitted, we check that the selected icon actually exists and that it comes from a set the field offers.

This matters because the state of the field is sent from the browser, so it can be changed. If somebody submits an icon that isn't real, or one from a set you excluded with `sets()`, validation fails.

"Exists" means the SVG is actually on disk, not just that the name looks right. So a record still pointing at an icon from a kit you uninstalled will fail validation the next time it is saved.

You don't have to configure anything for this. If you also use [custom icons](../custom-icons/01-enabling-uploads.md), the scope of the icon is verified as well.
