---
title: Installation
---

# Installation

## Requirements

- PHP 8.2 or higher
- Filament 5.x
- blade-icons 1.8 or higher, which composer installs for you

## Installing the package

You can install the package via composer:

```bash
composer require guava/filament-icon-picker
```

Next, publish the package assets:

```bash
php artisan filament:assets
```

## Custom theme

A custom filament theme is **required**, otherwise the CSS of the picker is not built and the field will look broken.

If you don't have one yet, please read the [filament documentation](https://filamentphp.com/docs/5.x/styling/overview#creating-a-custom-theme) on how to create a custom theme.

Once you have a theme, add the following to your **theme.css** file:

```css
@source '../../../../vendor/guava/filament-icon-picker/resources/**/*';
```

> [!IMPORTANT]
> Every panel that uses the icon picker needs a theme with this source path. It's up to you if you want to use the same theme for all panels or a separate one for each.

## Icon endpoints

The package registers two routes of its own, which is how the picker gets its icons into the browser:

```
/_icon-picker/index
/_icon-picker/svgs
```

You don't have to register or configure anything for this, it happens automatically. Both routes are secured by an encrypted token that is issued when the field renders, so they only ever hand out the sets and the scope that the field itself offers.

If you cache your routes, remember to rebuild the cache after installing:

```bash
php artisan optimize
```

The URI prefix and the middleware of both routes can be changed, see [configuration](advanced/04-configuration.md).

## Configuration

The package works without any configuration. If you want to change the caching, the routes or the token lifetime, publish the config file:

```bash
php artisan vendor:publish --tag=filament-icon-picker-config
```

Everything in it is explained in [configuration](advanced/04-configuration.md).

## Custom icon uploads

If you want your users to upload their own icons, you also need a public storage link, since uploaded icons are stored on the `public` disk:

```bash
php artisan storage:link
```

Everything else is covered in [enabling uploads](custom-icons/01-enabling-uploads.md).
