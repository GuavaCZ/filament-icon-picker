---
title: Installation
---

# Installation

## Requirements

- PHP 8.0 or higher
- Filament 2.x
- Laravel 9 or 10

## Installing the package

You can install the package via composer:

```bash
composer require guava/filament-icon-picker:"^1.0"
```

## Configuration

You can publish the config file with:

```bash
php artisan vendor:publish --tag="filament-icon-picker-config"
```

It ends up in `config/icon-picker.php` and lets you set the defaults for every picker in your application:

```php
return [
    'sets' => null,

    'columns' => 1,

    'layout' => \Guava\FilamentIconPicker\Layout::FLOATING,

    'cache' => [
        'enabled' => true,
        'duration' => '7 days',
    ],
];
```

Every one of these can also be set per field, which is covered in [options](04-options.md).

> [!NOTE]
> When you install a new icon set and it doesn't show up in the picker, please clear your cache. Search results are cached for 7 days by default.
