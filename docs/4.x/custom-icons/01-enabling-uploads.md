---
title: Enabling uploads
---

# Enabling uploads

Custom icon uploads are disabled by default. You can enable them per field:

```php
IconPicker::make('icon')
    ->customIconsUploadEnabled();
```

This adds an **Upload custom icon** action next to the label of the field. We also offer the same action in the results when a search returns nothing, since that is usually the moment your users notice the icon they want is missing.

> [!IMPORTANT]
> Uploaded icons are stored on the `public` disk, so you need a storage link:
> ```bash
> php artisan storage:link
> ```

## What your users see

The action opens a modal with two fields:

- **Icon**, which accepts a single SVG file up to 512 KB
- **Label**, which names the icon

The label may only contain letters, numbers and spaces. We turn it into the name of the icon, so a label of `My Nice Icon` becomes `my-nice-icon`.

After the upload the new icon is selected in the field right away, and it shows up in the picker together with all the other icons.

## Where the icons show up

Custom icons live in their own set, which is called **Custom icons** in the set dropdown.

The set is only offered on fields that have uploads enabled. A field without `customIconsUploadEnabled()` never lists custom icons, even if somebody uploaded one somewhere else in your panel.

If you restrict the sets of a field with `sets()`, please note that the custom set is filtered out along with the rest unless you include it.

## Icon names

Custom icons are prefixed with `_gfic_icons-`, followed by the scope and the name:

```
_gfic_icons-unscoped.my-nice-icon
```

That full string is what we store in your database, and it's what you render on your frontend. See [rendering on the frontend](../advanced/01-rendering-on-the-frontend.md).

If you upload icons for a specific record, the middle part is the scope of that record instead of `unscoped`. That is covered in [scoping to a record](02-scoping-to-a-record.md).
