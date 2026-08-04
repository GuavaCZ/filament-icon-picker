---
title: Configuration
---

# Configuration

The package works without any configuration. Publish the config file only when you want to change one of the defaults below:

```bash
php artisan vendor:publish --tag=filament-icon-picker-config
```

It ends up in `config/filament-icon-picker.php`.

> [!NOTE]
> This is a new file in 5.x. It has nothing to do with the old `config/icon-picker.php` from 2.x and 3.x, which is unused and can be deleted.

## Caching

```php
'cache' => [
    'enabled' => env('ICON_PICKER_CACHE_ENABLED', true),
    'duration' => '7 days',
    'prefix' => 'guava-icon-picker',
],
```

Listing an icon set means walking its directory, and the big kits contain thousands of files. So we cache the listing of every set, and the rendered markup of the icons from the kits you installed.

`duration` accepts anything `strtotime` understands.

You rarely need to touch this. The bundled kits don't change between deployments, and icons your users upload clear their own cache the moment they are uploaded.

The one case where it matters is a set of your own that you add SVGs to later. Those appear once you clear the cache:

```bash
php artisan cache:clear
```

If that gets in your way while you are still putting a set together, turn caching off in your local environment:

```dotenv
ICON_PICKER_CACHE_ENABLED=false
```

> [!NOTE]
> `php artisan optimize` does not clear this. It caches config, routes, events and views, it doesn't touch your application cache.

## Routes

```php
'routes' => [
    'prefix' => '_icon-picker',
    'middleware' => ['web', 'throttle:120,1'],
],
```

The picker gets its icons from two routes that the package registers for you:

| Route | What it returns |
|-------|-----------------|
| `/_icon-picker/index` | The sets and icon names a field may offer |
| `/_icon-picker/svgs` | The markup of up to 50 icons per request |

Change `prefix` if the default collides with a route of your own. Both route names stay the same, so nothing else has to change.

`middleware` is applied to both routes. The `web` group is there because the picker is used inside your panel, and the throttle is a safety net.

> [!IMPORTANT]
> Access control does not come from this middleware, it comes from the token described below. Adding your panel's auth middleware here is fine, but removing `web` or replacing the list with something unrelated is not something we test for.

Remember to rebuild your route cache after changing the prefix:

```bash
php artisan optimize
```

## Token lifetime

```php
'token_lifetime' => '12 hours',
```

Every rendered field issues a token that carries the sets and the [scope](../custom-icons/02-scoping-to-a-record.md) it may serve. It is encrypted with your `APP_KEY`, so the browser can't read or change it, and the endpoints trust nothing else.

This is what keeps the endpoints safe without any setup on your side. A token is only ever handed to somebody who was already allowed to render the form it belongs to, and it can only ever unlock exactly what that field offers.

The lifetime bounds how long a token stays usable. A form that stayed open longer than this needs a page reload before icons load again, so raise it if your users keep forms open for a very long time.

> [!NOTE]
> Rotating your `APP_KEY` invalidates every issued token immediately, the same way it invalidates sessions. Open forms need a reload after a key rotation.
