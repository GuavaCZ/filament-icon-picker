---
title: Icon sets
---

# Icon sets

The picker doesn't ship any icons of its own. It reads whatever is registered with [blade-icons](https://github.com/blade-ui-kit/blade-icons), which means every kit you install shows up in the picker automatically.

Filament ships with heroicons, so that set is always there.

## Installing more sets

There are a lot of ready made kits for blade-icons. You can install any of them via composer, for example:

```bash
composer require blade-ui-kit/blade-bootstrap-icons
```

That's all. The new set appears in the picker's set dropdown the next time you open it.

A full list of available kits is on the [blade-icons website](https://blade-ui-kit.com/blade-icons#search).

> [!NOTE]
> Some kits are very large, but only the icon names are sent to the browser, and the SVGs are loaded as they scroll into view. Installing several big kits is fine. See [search behaviour](05-search-behaviour.md).

## Registering your own set

Any directory you register with blade-icons shows up in the picker, so you can offer your own SVGs without the upload action:

```php
// config/blade-icons.php
'sets' => [
    'my-icons' => [
        'path' => 'resources/icons',
        'prefix' => 'my-icon',
    ],
],
```

> [!IMPORTANT]
> Icon listings are cached. If you add SVGs to a set of your own after it was first used, run `php artisan cache:clear` to see them. See [configuration](../advanced/04-configuration.md).

## Limiting the available sets

By default, all sets that are registered in your application are available in the picker.

If you only want to offer some of them, pass their IDs to the `sets` option:

```php
IconPicker::make('icon')
    ->sets(['heroicons']);
```

The ID of a set is the name it was registered under with blade-icons, not the prefix of its icons. For heroicons the ID is `heroicons`, while its icons are prefixed with `heroicon-`.

If you are unsure what a kit registered itself as, you can list all sets:

```php
use Guava\IconPicker\Icons\Facades\IconManager;

IconManager::getSets()->keys();
```

> [!NOTE]
> `sets()` is not only a visual restriction. Icons from sets you excluded are rejected on submit as well, so you can rely on it.
