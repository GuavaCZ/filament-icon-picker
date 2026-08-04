---
title: Upgrading
---

# Upgrading

This guide covers the upgrade from 4.x to 5.x.

Unlike 4.x, this major is not a filament upgrade. It still runs on filament 5, and the public API of the field is unchanged. What changed is underneath: the picker no longer loads icons over livewire, it serves them from two package routes secured by an encrypted per-field token, and icon listings are cached.

For almost every app the upgrade is two commands.

## Upgrade

```bash
composer require guava/filament-icon-picker:"^5.0"
php artisan filament:assets
```

That's it. `sets()`, `scopedTo()`, `dropdown()`, `searchResultsView()` and everything else on the field work exactly as before.

If you cache anything in deployment, run:

```bash
php artisan optimize
```

The package registers two new routes and its blade view changed, so the route cache and the compiled views need rebuilding. `optimize` does both, and most deploy scripts run it already, so in practice there is nothing extra to do.

## Only if you published the views

Skip this unless you have a `resources/views/vendor/guava-icon-picker` directory.

The field view talks to livewire methods that don't exist anymore, so a published copy of it would leave the picker without icons. Publish it again:

```bash
php artisan vendor:publish --tag=guava-icon-picker-views --force
```

Custom search result views are **not** affected. The shape of an icon in the browser is unchanged, so `icon.id`, `icon.label`, `icon.set` and `icon.custom` all still work. See [custom search results view](advanced/02-custom-search-results-view.md).

## Only if you extended the field

Three methods that drove the old livewire bridge are gone. They were internal plumbing for our own view:

| Removed | What to do |
|---------|------------|
| `getSetJs()` | No replacement, the browser gets the sets from the index endpoint |
| `getIconsJs()` | Use `IconManager::getIcons()` on the server |
| `getIconSvgJs()` | No replacement, the browser fetches SVGs in batches from the svg endpoint |
| `Forms\Concerns\CanBeCacheable` | No replacement. The trait was already unused in 4.x, caching is configured globally now |

`verifyState()`, `resolveIcon()` and `getAllowedSets()` are unchanged.

## Only if you register your own icon sets

Icon listings are cached for 7 days now. Uploaded custom icons clear their own cache and the bundled kits never change, so this only shows up when you add SVGs to a set you registered yourself. They appear after:

```bash
php artisan cache:clear
```

Note that `php artisan optimize` does **not** do this. It caches framework files, it doesn't touch your application cache.

To shorten the duration or turn caching off completely, see [configuration](advanced/04-configuration.md).

## Good to know

Nothing to do here, but a few things behave differently.

### Icons are validated more strictly

An icon used to pass validation as long as it started with a registered set prefix, so `heroicon-o-does-not-exist` was accepted. Now the SVG has to actually exist.

This is a fix, but it means stored values pointing at icons that are gone will fail when the record is saved next. If you swapped or uninstalled an icon kit at some point, it's worth checking:

```php
use Guava\IconPicker\Icons\Facades\IconManager;

Category::query()
    ->whereNotNull('icon')
    ->pluck('icon', 'id')
    ->reject(fn (string $icon) => (bool) IconManager::getIcon($icon))
    ->dump();
```

Anything listed there no longer resolves and should be fixed or nulled out.

Icon sets are expected to contain `.svg` files. Please open an issue if you registered one that doesn't.

### The selected icon label lost its set prefix

The name shown for the **selected** icon used to include the prefix, so it read `Heroicon o academic cap` while the same icon in the list read `O academic cap`. Both now read `O academic cap`.

### Searching feels different

The browser side search moved from Fuse.js to [uFuzzy](https://github.com/leeoniya/uFuzzy), so results come back in a slightly different order. What is being searched did not change, see [search behaviour](field/05-search-behaviour.md).

### Icons load once per page

Fields that offer the same sets and the same scope now share a single icon download, and every SVG is fetched once and reused across all pickers on the page. Rendering several pickers in one form is a lot cheaper than it was.

### Forms left open for a long time

Picker tokens are valid for 12 hours. A form that stayed open longer than that needs a page reload before icons load again. The lifetime is configurable, see [configuration](advanced/04-configuration.md).

## Found an issue?

If you find a step that is missing here, please open a PR and modify this file. We will review it and merge it.
