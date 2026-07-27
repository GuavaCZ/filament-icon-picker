---
title: Upgrading
---

# Upgrading

This guide covers the upgrade from 3.x to 4.x.

Version 4.x exists for filament 5. There are no changes to the public API of the package, so in most cases you only need to upgrade filament itself and bump the constraint.

## Upgrade filament

Please follow the [filament upgrade guide](https://filamentphp.com/docs/5.x/upgrade-guide) first. If your panels don't run on filament 5 yet, the picker won't work either.

## Bump the constraint

```bash
composer require guava/filament-icon-picker:"^4.0"
```

Then republish the assets, since the built JS changed:

```bash
php artisan filament:assets
```

## Check your theme

The source path in your **theme.css** is unchanged:

```css
@source '../../../../vendor/guava/filament-icon-picker/resources/**/*';
```

If you never added it, please read the [installation](02-installation.md) page, as a custom theme is required.

## Found an issue?

If you find a step that is missing here, please open a PR and modify this file. We will review it and merge it.
