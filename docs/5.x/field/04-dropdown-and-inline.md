---
title: Dropdown and inline
---

# Dropdown and inline

By default the icon picker opens a dropdown where you search and select the icon, very similar to a regular `Select` field in filament.

## Rendering the picker inline

If you prefer, you can disable the dropdown. The search field and the results are then rendered directly beneath the field:

```php
IconPicker::make('icon')
    ->dropdown(false);
```

This works well on a dedicated settings page where the picker is the main thing on screen. In a modal or a dense form the dropdown is usually the better choice.

> [!NOTE]
> The inline layout loads its icons as soon as the form is rendered, while the dropdown waits until it is opened for the first time. Only the icon names are loaded either way, so the difference is small. See [search behaviour](05-search-behaviour.md).

## Closing the dropdown on select

By default the dropdown stays open after picking an icon, so your users can try a few in a row and see the result in the field.

If you would rather close it right after the selection:

```php
IconPicker::make('icon')
    ->closeOnSelect();
```

This option does nothing when you render the picker inline, since there is no dropdown to close.
