---
title: Search behaviour
---

# Search behaviour

Searching happens in the browser. The picker downloads the list of icons it may offer once, and every keystroke is then matched against that list with [uFuzzy](https://github.com/leeoniya/uFuzzy), so nothing hits your server while your users type.

The results are rendered in chunks while you scroll, so even a set with thousands of icons doesn't slow the page down.

## When the icons are loaded

Only the names of the icons are downloaded up front, never the SVGs. That list is small enough to cover every set you offer at once.

When the picker is a dropdown, the download starts the first time it is opened. When you render it [inline](04-dropdown-and-inline.md), it starts as soon as the form is rendered, because the results are visible right away.

The SVGs themselves are fetched afterwards, in batches, and only for the icons that actually scroll into view. This is also why switching sets is instant, the set dropdown only filters a list the browser already has.

> [!NOTE]
> Pickers that offer the same sets and the same scope share one download, and a rendered SVG is reused by every picker on the page. Several pickers in one form cost barely more than a single one.

## Customizing the messages

The picker uses the standard filament search options, so you can change the texts it shows.

### Search prompt

The placeholder in the search input:

```php
IconPicker::make('icon')
    ->searchPrompt('Search icons by name');
```

### Searching message

Shown while the icons are being loaded:

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

A single typo per word is tolerated, so `acadmic` still finds `heroicon-o-academic-cap`. The first letter has to be right though, and you can narrow the results with several words at once, as in `heroicon academic`.
