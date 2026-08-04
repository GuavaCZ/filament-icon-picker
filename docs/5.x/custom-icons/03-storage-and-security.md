---
title: Storage and security
---

# Storage and security

## Where the files are stored

Uploaded icons are stored on the `public` disk, in a directory per scope:

```
storage/app/public/icon-picker-icons/unscoped/my-nice-icon.svg
storage/app/public/icon-picker-icons/<scope>/my-nice-icon.svg
```

We register that directory as a blade-icons set, which is what makes the uploaded files behave like any other icon.

Because they live on the `public` disk, the files are also reachable directly under `/storage`. Keep that in mind when you decide who may upload.

## SVG sanitizing

SVG is not only an image format. It can contain scripts, event handlers and references to other files, and it is rendered inline into your panel, so an unchecked upload would run in the browser of everybody who sees that icon.

For that reason we don't store the uploaded file as it is. We reduce it to what an icon actually needs and drop everything else, before anything is written to disk.

What survives:

- The shapes: `path`, `rect`, `circle`, `ellipse`, `line`, `polyline`, `polygon`, `g`, `defs`, `title`, `desc`
- Gradients, masks, clip paths and patterns
- The presentation attributes you would expect, such as `fill`, `stroke`, `viewBox`, `transform` and `opacity`

What is removed:

- `<script>` and every `on...` event handler
- `<style>` and `style` attributes
- `href`, `xlink:href` and every namespaced attribute
- `<use>`, `<image>`, `<foreignObject>` and the animation elements
- Comments, processing instructions and doctypes
- Any `url()` that doesn't point at a local reference like `url(#gradient)`

If the file cannot be parsed as an SVG at all, the upload is rejected with a validation error.

> [!NOTE]
> Icons that rely on `<style>` blocks or on `<use>` references will look different after the upload, because those parts are stripped. Icons drawn with plain paths and presentation attributes, which is what icon kits produce, come through unchanged.

## How the picker reads them back

Icons are served to the browser by two endpoints the package registers, not by livewire. Both are gated by an encrypted token that the field issues when it renders, and that token carries the sets and the scope the field is allowed to serve.

That means the endpoints never widen what a field offers. A token issued for a field scoped to one record cannot list or render the icons of another, and a token from a field without uploads enabled cannot reach the custom set at all. See [configuration](../advanced/04-configuration.md) for the lifetime and the route options.

## Icon names

The label your users type ends up in the filename, so it is restricted to letters, numbers and spaces. That is checked on the server, not only in the input.

This stops a label from containing path separators, which would otherwise let an upload land outside the directory it belongs to.

## Who may upload

The package does not decide who may upload an icon. `customIconsUploadEnabled()` accepts a closure, so use your own authorization:

```php
IconPicker::make('icon')
    ->customIconsUploadEnabled(fn () => auth()->user()->can('uploadIcons'));
```

> [!IMPORTANT]
> Anyone who can upload an icon can put a file on your public disk that other users of the same scope will see. Please treat it as you would any other file upload and only enable it for users you trust.
