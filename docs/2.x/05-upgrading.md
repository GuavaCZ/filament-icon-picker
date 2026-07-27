---
title: Upgrading
---

# Upgrading

This guide covers the upgrade from 1.x to 2.x. Version 2.x exists for filament 3.

The API of the package is unchanged between the two, so in most cases you only need to upgrade filament itself and bump the constraint.

## Upgrade filament

Please follow the [filament upgrade guide](https://filamentphp.com/docs/3.x/upgrade-guide) first. If your panels don't run on filament 3 yet, the picker won't work either.

## Bump the constraint

```bash
composer require guava/filament-icon-picker:"^2.0"
```

Your imports, your config file and every option stay the same.
