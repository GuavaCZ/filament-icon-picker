---
title: Rendering on the frontend
---

# Rendering on the frontend

We store the full icon name in your database, which means there is nothing package specific about rendering it. Treat it as you would any other static icon.

Assuming you saved the icon on your `$category` model under `icon`, you can render it in any blade view with the blade-icons component:

```blade
<x-icon :name="$category->icon" />
```

You can pass classes and attributes as usual:

```blade
<x-icon :name="$category->icon" class="w-6 h-6 text-primary-600" />
```

More information on rendering icons is in the [blade-icons documentation](https://github.com/blade-ui-kit/blade-icons#default-component).

## Custom icons

Icons your users uploaded work the same way. Their names look like `_gfic_icons-unscoped.my-nice-icon`, but that is still just an icon name to blade-icons:

```blade
<x-icon :name="$category->icon" />
```

## Icons that no longer exist

Blade-icons throws an exception when it cannot find an icon. This can happen if you uninstall a kit, or if a custom icon was deleted from the disk while a record still points at it.

To be safe on a public page, either configure a fallback icon in your `config/blade-icons.php`, or check the icon before you render it:

```php
use Guava\IconPicker\Icons\Facades\IconManager;

if (IconManager::getIcon($category->icon)) {
    // Safe to render
}
```

Inside filament you don't need this. Our field and column both fall back to their placeholder when an icon doesn't resolve.
