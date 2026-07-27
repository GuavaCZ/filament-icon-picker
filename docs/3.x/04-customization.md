---
title: Customization
---

# Customization

## Search result views

Out of the box we provide three different views for the search results.

### Grid view

This is the default. Icons are shown in a grid with their name underneath.

```php
IconPicker::make('icon')
    ->gridSearchResults();
```

### List view

Icons are rendered in a list together with their name.

```php
IconPicker::make('icon')
    ->listSearchResults();
```

### Icons view

Icons are rendered in a compact grid with only the icons visible, which fits the most icons on screen. By default each one has a tooltip with its name:

```php
IconPicker::make('icon')
    ->iconsSearchResults();      // With tooltips

IconPicker::make('icon')
    ->iconsSearchResults(false); // Without tooltips
```

### Your own view

If none of the three fit, you can render the results yourself:

```php
IconPicker::make('icon')
    ->searchResultsView('icon-picker.my-results');
```

The results are rendered by alpine, so your view is a template it fills in. `resultsVisible` holds the icons to render, `updateState(icon)` selects one, `setElementIcon($el, icon.id)` draws one into an element and `addSearchResultsChunk()` loads the next chunk as the user scrolls.

The views that ship with the package are in `resources/views/search-results` and are the easiest thing to copy from.

## Dropdown

By default the picker opens a dropdown, very similar to a regular `Select` field in filament.

If you prefer, you can disable it. The search and the results are then rendered directly beneath the field:

```php
IconPicker::make('icon')
    ->dropdown(false);
```

## Limiting the available sets

By default all sets registered in your application are available. If you only want to offer some of them, pass their IDs:

```php
IconPicker::make('icon')
    ->sets(['heroicons']);
```

The ID of a set is the name it was registered under with blade-icons, not the prefix of its icons. For heroicons the ID is `heroicons`, while its icons are prefixed with `heroicon-`.
