---
title: Installation
---

# Installation

## Requirements

- PHP 8.2 or higher
- Filament 4.x
- blade-icons 1.8 or higher, which composer installs for you

## Installing the package

You can install the package via composer:

```bash
composer require guava/filament-icon-picker:"^3.0"
```

Next, publish the package assets:

```bash
php artisan filament:assets
```

## Custom theme

A custom filament theme is **required**, otherwise the CSS of the picker is not built and the field will look broken.

If you don't have one yet, please read the [filament documentation](https://filamentphp.com/docs/4.x/styling/overview#creating-a-custom-theme) on how to create a custom theme.

Once you have a theme, add the following to your **theme.css** file:

```css
@source '../../../../vendor/guava/filament-icon-picker/resources/**/*';
```

> [!IMPORTANT]
> Every panel that uses the icon picker needs a theme with this source path. It's up to you if you want to use the same theme for all panels or a separate one for each.

## Livewire payload size

The picker loads the icons of your installed sets over livewire. If you have a lot of icon sets installed, you might hit livewire's payload limit, especially when you render the picker inline.

If that happens, increase the `payloads.max_calls` setting in your `config/livewire.php`.
