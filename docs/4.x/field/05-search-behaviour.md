---
title: Search behaviour
---

# Search behaviour

Searching happens in the browser. We send the icons of the selected set to the frontend once, and the search itself then runs over that list with [Fuse.js](https://fusejs.io), which is why it stays fast and doesn't hit your server on every keystroke.

The results are rendered in chunks while you scroll, so even a set with thousands of icons doesn't slow the page down.

## Customizing the messages

The picker uses the standard filament search options, so you can change the texts it shows.

### Search prompt

The placeholder in the search input:

```php
IconPicker::make('icon')
    ->searchPrompt('Search icons by name');
```

### Searching message

Shown while the icons of a set are being loaded:

```php
IconPicker::make('icon')
    ->searchingMessage('Loading icons...');
```

### No results message

Shown when the search returns nothing:

```php
IconPicker::make('icon')
    ->noSearchResultsMessage('No icon matches this name.');
```

> [!NOTE]
> If you enabled [custom icons](../custom-icons/01-enabling-uploads.md), this is also where we offer the upload link, so your users can add the icon they were looking for.

## What is being searched

We search the icon IDs, so `academic` matches `heroicon-o-academic-cap`. This means the prefix of a set is searchable too, and typing `heroicon-s` narrows the results down to the solid heroicons.
