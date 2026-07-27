---
title: Options
---

# Options

All of these can be set per field. The defaults for `sets`, `columns`, `layout` and `cache` also live in the config file, see [installation](02-installation.md).

## Columns

By default a single column picker is displayed. You can change that with `columns`:

```php
// Display 3 columns from lg and above
IconPicker::make('icon')
    ->columns(3);
```

You can also pass an array with breakpoints. This displays 1 column from xs to md, 3 columns from lg to xl and 5 columns from 2xl and above:

```php
IconPicker::make('icon')
    ->columns([
        'default' => 1,
        'lg' => 3,
        '2xl' => 5,
    ]);
```

## Sets

By default all installed blade-icons sets are used. If you only want specific ones:

```php
// Search both heroicons and fontawesome icons
IconPicker::make('icon')
    ->sets(['heroicons', 'fontawesome-solid']);
```

> [!NOTE]
> When you install new sets, please make sure to clear your cache if you can't find your icons in the picker.

## Allowing and disallowing icons

For more detailed control there are two options:

```php
// Allow ONLY heroicon-o-user and heroicon-o-users
IconPicker::make('icon')
    ->allowIcons(['heroicon-o-user', 'heroicon-o-users']);
```

```php
// Allow ALL icons, EXCEPT fas-user
IconPicker::make('icon')
    ->disallowIcons(['fas-user']);
```

## Layout

The picker comes with two layouts.

`Layout::FLOATING` is the default and behaves like a regular filament select, the results appear in a popover.

`Layout::ON_TOP` renders the results directly on the page, in a catalogue like grid.

```php
use Guava\FilamentIconPicker\Layout;

IconPicker::make('icon')
    ->layout(Layout::FLOATING); // default

IconPicker::make('icon')
    ->layout(Layout::ON_TOP);
```

## Custom item template

Out of the box the results render a preview of the icon and its identifier. You can replace that with your own view:

```php
// Make sure to pass the $icon to your view so you can render it there
IconPicker::make('icon')
    ->itemTemplate(
        fn ($icon) => view('your.blade.template', ['icon' => $icon])
    );
```

## Caching

Icon packs often contain a lot of icons, so searching through all of them can take a while. To keep this bearable, search results are cached for 7 days by default.

You can configure the defaults in the config file, or per field:

```php
IconPicker::make('icon')
    // Disable caching
    ->cacheable(false)

    // Cache for one hour
    ->cacheDuration(3600);
```
