---
title: Scoping to a record
---

# Scoping to a record

By default, uploaded icons are unscoped. Every field with uploads enabled offers them, which is what you want for a set of icons that belongs to your whole application.

In a multi tenant app that is usually not what you want. If one of your customers uploads their logo, the other customers should not see it in their picker.

For that, scope the field to a model:

```php
IconPicker::make('icon')
    ->customIconsUploadEnabled()
    ->scopedTo(fn () => Filament::getTenant());
```

The option accepts a model or a closure, so you can also scope to the record you are editing:

```php
IconPicker::make('icon')
    ->customIconsUploadEnabled()
    ->scopedTo(fn (?Category $record) => $record);
```

From then on, icons uploaded through that field belong to that record. The picker only offers icons of the same scope, and unscoped icons are not offered either.

## How the scope is built

The scope is derived from the morph class and the key of the model, so two different models never end up sharing icons. You don't have to do anything for this, it happens automatically as soon as you pass a model to `scopedTo()`.

## Scopes are enforced, not just filtered

The state of the field is sent from the browser, which means it can be changed. Filtering the list on its own would not stop anybody from submitting the name of an icon that belongs to somebody else.

So the scope is also checked when the form is submitted, and again whenever the picker asks the server to render an icon. An icon from a different scope is rejected in both places.

This applies in the other direction as well. A field **without** a scope only accepts unscoped icons, so you cannot accidentally leak a customer's icon into a global setting.

> [!IMPORTANT]
> The scope has to be resolvable at the time the field is rendered. If you scope to `$record` on a create form, there is no record yet, so the field falls back to unscoped icons. If that is a problem for your use case, scope to the tenant or to the parent record instead.

## Changing the scope later

The scope is part of the icon name we store, so moving a record between tenants does not move its icons. If you need to do that, you have to move the files on the `public` disk and update the stored icon names yourself.
